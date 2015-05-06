<?php

namespace Mesour\DataGrid\Extensions;

use Mesour\DataGrid\Column,
    Mesour\DataGrid\Grid_Exception,
    Nette\Application\Responses\FileResponse,
    Nette\Utils\Strings;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Export extends BaseControl {

	/**
	 * Cache directory
	 *
	 * @var string
	 */
	private $cache_dir;

	/**
	 * Restriction for some columns
	 *
	 * @var array
	 */
	private $export_columns = array();

	private $file_name = NULL;

	private $delimiter = ',';

	private $file_path;

	public function setFileName($file_name) {
		if (!is_string($file_name) && !is_null($file_name)) {
			throw new Grid_Exception('Export file name must be string, ' . gettype($file_name) . ' given.');
		}
		$this->file_name = $file_name;
	}

	public function setDelimiter($delimiter = ",") {
		$this->delimiter = $delimiter;
	}

	public function setCacheDir($dir) {
		if (!is_dir($dir)) {
			throw new Grid_Exception('Cache dir is not a directory.');
		}
		if (!is_writable($dir)) {
			throw new Grid_Exception('Cache dir is not a writable.');
		}
		$this->cache_dir = $dir;
	}

	public function setColumns(array $columns = array()) {
		$this->export_columns = $columns;
	}

	public function render() {
		$this->template->setFile(dirname(__FILE__) . '/templates/Export.latte');
		$this->template->render();
	}

	public function hasExport($column) {
		return ($column instanceof Column\Text || $column instanceof Column\Number
		|| $column instanceof Column\Date || $column instanceof Column\Container
		    || $column instanceof Column\Template)
			&& (!$column instanceof Column\Container || ($column instanceof Column\Container && $column->hasExportableColumns()));
	}

	public function handleExport() {
		$header_arr = array();
		$export_columns = array();

		if (isset($this->parent['filter'])) {
			$this->parent['filter']->applyFilter();
		}

		if (empty($this->export_columns)) {
			foreach ($this->parent->getColumns() as $column) {
				$column->setGridComponent($this->parent);
				if ($this->hasExport($column)) {
					$export_columns[] = $column;
				}
			}
		} else {
			foreach ($this->export_columns as $column_val) {
				$used_in_columns = FALSE;
				foreach ($this->parent->getColumns() as $column) {
					if ($this->hasExport($column)) {
						if (is_array($column_val)) {
							$column_name = key($column_val);
						} else {
							$column_name = $column_val;
						}
						if ($column_name === $column->getId()) {
							$export_columns[] = $column;
							$used_in_columns = TRUE;
						}
					}
				}
				if ($used_in_columns === FALSE) {
					$export_columns[] = $column_val;
				}
			}
		}
		$this->file_path = $this->cache_dir . "/" . Strings::webalize($this->parent->getGridName()) . time() . ".csv";
		$file = fopen($this->file_path, "w");
		fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // add BOM to fix UTF-8 in Excel
		foreach ($export_columns as $column) {
			if ($column instanceof Column\IColumn) {
				if ($this->parent->getTranslator()) {
					$column->setTranslator($this->parent->getTranslator());
				}
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

		$first = TRUE;
		foreach ($this->parent->getDataSource()->fetchAllForExport() as $data) {
			$line_data = array();
			foreach ($export_columns as $column) {
				if ($column instanceof Column\IColumn) {
					$line_data[] =  strip_tags($column->getBodyContent($data, TRUE));
				} else {
					if (is_array($column)) {
						$column_name = key($column);
					} else {
						$column_name = $column;
					}
					if ($first && !isset($data[$column_name]) && !is_null($data[$column_name])) {
						throw new Grid_Exception('Column "' . $column_name . '" does not exist in data.');
					}
					$line_data[] = strip_tags($data[$column_name]);
				}
			}
			fputcsv($file, $line_data, $this->delimiter);
			$first = FALSE;
		}
		fclose($file);

		$this->presenter->sendResponse(new FileResponse($this->file_path, (is_null($this->file_name) ? $this->parent->getGridName() : $this->file_name) . '.csv'));
	}

	public function __destruct() {
		if (is_file($this->file_path)) {
			unlink($this->file_path);
		}
	}

}
