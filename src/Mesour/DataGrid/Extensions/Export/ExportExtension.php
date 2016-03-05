<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\Export;

use Mesour;


/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class ExportExtension extends Mesour\DataGrid\Extensions\Base implements IExport, Mesour\DataGrid\Extensions\IExtension
{

	/**
	 * Cache directory
	 * @var string
	 */
	private $cache_dir;

	/**
	 * Restriction for some columns
	 * @var array
	 */
	private $export_columns = [];

	/** @var string|null */
	private $fileName = null;

	private $delimiter = ',';

	private $file_path;

	/** @var Mesour\UI\Button */
	private $createdExport;

	public function setFileName($file_name)
	{
		if (!is_string($file_name) && !is_null($file_name)) {
			throw new Mesour\InvalidArgumentException(
				sprintf('Export file name must be string, %s given.', gettype($file_name))
			);
		}
		$this->fileName = $file_name;
		return $this;
	}

	public function setDelimiter($delimiter = ",")
	{
		$this->delimiter = $delimiter;
		return $this;
	}

	public function setCacheDir($dir)
	{
		if (!is_dir($dir)) {
			throw new Mesour\DirectoryNotFoundException('Cache dir is not a directory.');
		}
		if (!is_writable($dir)) {
			throw new Mesour\InvalidStateException('Cache dir is not a writable.');
		}
		$this->cache_dir = $dir;
		return $this;
	}

	public function setColumns(array $columns = [])
	{
		$this->export_columns = $columns;
		return $this;
	}

	/**
	 * @return Mesour\UI\Button|Mesour\UI\DropDown
	 */
	public function getExportButton()
	{
		if (!isset($this['button'])) {
			$filter = $this->getGrid()->getExtension('IFilter', false);
			if ($filter instanceof Mesour\DataGrid\Extensions\Filter\IFilter) {
				$this['button'] = $dropdown = new Mesour\UI\DropDown;

				$dropdown->setAttribute('class', 'show-export', true)
					->setPullRight();

				if ($filter instanceof Mesour\DataGrid\Extensions\Filter\IFilter) {
					$dropdown->addButton('Export filtered')
						->setAttribute('href', $this->createLink('export', ['type' => 'filtered']));
				}

				$dropdown->addDivider();
				$dropdown->addButton('Export all')
					->setAttribute('href', $this->createLink('export'));

				$button = $dropdown->getMainButton();
			} else {
				$this['button'] = $button = new Mesour\UI\Button;
				$button->setAttribute('class', 'show-export', true)
					->setAttribute('href', $this->createLink('export'));
			}
			$button->setType('primary')
				->setText('Export');

		}
		return $this['button'];
	}

	public function gridCreate($data = [])
	{
		parent::create();

		$this->createdExport = $this->getExportButton();

		$selection = $this->getGrid()->getExtension('ISelection', false);

		if ($selection instanceof Mesour\DataGrid\Extensions\Selection\ISelection) {
			if (count($selection->getLinks()->getLinks())) {
				$selection->getLinks()
					->addDivider();
			}
			$selection->getLinks()
				->addLink('Export to CSV')
				->setAjax(false)
				->onCall[] = function (array $selectedItems) {
				$ids = array_keys($selectedItems, 'true');
				$this->handleExport('selected', $ids);
			};
		}
	}

	public function attachToRenderer(Mesour\DataGrid\Renderer\IGridRenderer $renderer, $data = [], $rawData = [])
	{
		$this->createdExport->setOption('data', $data);
		$renderer->setComponent('export', $this->createdExport->create());
	}

	public function hasExport(Mesour\Components\ComponentModel\IContainer $column)
	{
		return ($column instanceof Mesour\DataGrid\Column\IExportable);
	}

	public function handleExport($type = 'all', array $selectedIds = [])
	{
		if ($this->isDisabled()) {
			throw new Mesour\InvalidStateException('Cannot edit cell if extension is disabled.');
		}

		/** @var Mesour\DataGrid\Column\IColumn|array|Mesour\DataGrid\Column\BaseColumn $column */
		$header_arr = [];
		$export_columns = [];

		if (empty($this->export_columns)) {
			foreach ($this->getGrid()->getColumns() as $column) {
				if ($this->hasExport($column) && $column->isAllowed()) {
					$export_columns[] = $column;
				}
			}
		} else {
			foreach ($this->export_columns as $column_val) {
				$used_in_columns = false;
				foreach ($this->getGrid()->getColumns() as $column) {
					/** @var Mesour\DataGrid\Column\BaseColumn $column */
					if ($this->hasExport($column) && $column->isAllowed()) {
						if (is_array($column_val)) {
							$column_name = key($column_val);
						} else {
							$column_name = $column_val;
						}
						if ($column_name === $column->getName()) {
							$export_columns[] = $column;
							$used_in_columns = true;
						}
					}
				}
				if ($used_in_columns === false) {
					$export_columns[] = $column_val;
				}
			}
		}
		$this->file_path = $this->createFilePath();
		$file = fopen($this->file_path, "w");
		fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // add BOM to fix UTF-8 in Excel
		foreach ($export_columns as $column) {
			if ($column instanceof Mesour\DataGrid\Column\IColumn) {
				$header_arr[] = $column->getHeader();
			} else {
				if (is_array($column)) {
					$header_arr[] = reset($column);
				} else {
					$header_arr[] = $column;
				}
			}
		}
		fputcsv($file, $header_arr, $this->delimiter);
		$first = true;
		$exportData = [];
		switch ($type) {
			case 'selected' :
				$source = $this->getGrid()->getSource();
				$allData = $source->fetchAll();

				foreach ($selectedIds as $selectedId) {
					foreach ($allData as $currentData) {
						if ($selectedId == $currentData[$source->getPrimaryKey()]) {
							$exportData[] = $currentData;
							break;
						}
					}
				}
				break;
			case 'filtered' :
				$exportData = $this->getGrid()->getSource()->fetchForExport();
				break;
			default :
				$exportData = $this->getGrid()->getSource()->fetchFullData();
		}
		$rawData = $this->getGrid()->getSource()->fetchLastRawRows();
		foreach ($exportData as $key => $data) {
			$line_data = [];
			foreach ($export_columns as $column) {
				if ($column instanceof Mesour\DataGrid\Column\IColumn) {
					$line_data[] = strip_tags($column->getBodyContent($data, $rawData[$key], true));
				} else {
					if (is_array($column)) {
						$column_name = key($column);
					} else {
						$column_name = $column;
					}
					if ($first && !isset($data[$column_name]) && !is_null($data[$column_name])) {
						throw new Mesour\OutOfRangeException('Column "' . $column_name . '" does not exist in data.');
					}
					$line_data[] = strip_tags($data[$column_name]);
				}
			}
			fputcsv($file, $line_data, $this->delimiter);
			$first = false;
		}
		fclose($file);

		$this->download($this->file_path, (is_null($this->fileName) ? $this->getGrid()->createLinkName() : $this->fileName) . '.csv');
	}

	protected function createFilePath()
	{
		return sprintf(
			$this->cache_dir . '/%s%s.csv',
			Mesour\Components\Utils\Helpers::webalize($this->getGrid()->createLinkName()),
			time()
		);
	}

	private function download($filePath, $fileName)
	{
		ob_clean();
		header('Content-Type: applications/octet-stream');
		header('Content-Disposition: attachment; filename="' . $fileName . '"');
		header('Cache-Control: private, max-age=0, must-revalidate');
		header('Pragma: public');
		echo file_get_contents($filePath);
		exit(1);
	}

	public function __destruct()
	{
		if (is_file($this->file_path)) {
			unlink($this->file_path);
		}
	}

}