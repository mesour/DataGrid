<?php

namespace DataGrid\Extensions;

use DataGrid\Column;

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

	public function handleExport() {
		$header_arr = array();

		if (isset($this->parent['filter'])) {
			$this->parent['filter']->applyFilter();
		}

		$file_name = $this->cache_dir . "/" . $this->parent->getGridName() . time() . ".csv";
		$file = fopen($file_name, "w");
		foreach ($this->parent->getColumns() as $column) {
			if ($column instanceof Column\Text || $column instanceof Column\Number || $column instanceof Column\Date) {
				if (empty($this->export_columns) || in_array($column->getId(), $this->export_columns)) {
					$header_arr[] = $column->getText();
				}
			}
		}
		fputcsv($file, $header_arr);
		foreach ($this->parent->getDataSource()->fetchAllForExport() as $data) {
			$line_data = array();
			foreach ($this->parent->getColumns() as $column) {
				if ($column instanceof Column\Text || $column instanceof Column\Number || $column instanceof Column\Date) {
					if (empty($this->export_columns) || in_array($column->getId(), $this->export_columns)) {
						$line_data[] = $data[$column->getId()];
					}
				}
			}
			fputcsv($file, $line_data);
		}
		fclose($file);

		echo file_get_contents($file_name);
		unlink($file_name);

		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $this->parent->getGridName() . '.csv"');
		header('Content-Transfer-Encoding: binary');
		header('Connection: Keep-Alive');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		exit;
	}

}