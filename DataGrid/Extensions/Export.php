<?php

namespace DataGrid\Extensions;

use DataGrid\Column,
	DataGrid\Grid_Exception;

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

	public function setFileName($file_name) {
		if(!is_string($file_name) && !is_null($file_name)) {
			throw new Grid_Exception('Export file name must be string, ' . gettype($file_name) . ' given.');
		}
		$this->file_name = $file_name;
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

	public function handleExport() {
		$header_arr = array();
		$export_columns = array();

		if (isset($this->parent['filter'])) {
			$this->parent['filter']->applyFilter();
		}

		if(empty($this->export_columns)) {
			foreach ($this->parent->getColumns() as $column) {
				if ($column instanceof Column\Text || $column instanceof Column\Number || $column instanceof Column\Date) {
					$export_columns[] = $column;
				}
			}
		} else {
			foreach($this->export_columns as $column_val) {
				$used_in_columns = FALSE;
				foreach ($this->parent->getColumns() as $column) {
					if ($column instanceof Column\Text || $column instanceof Column\Number || $column instanceof Column\Date) {
						if(is_array($column_val)) {
							$column_name = key($column_val);
						} else {
							$column_name = $column_val;
						}
						if($column_name === $column->getId()) {
							$export_columns[] = $column;
							$used_in_columns = TRUE;
						}
					}
				}
				if($used_in_columns === FALSE) {
					$export_columns[] = $column_val;
				}
			}
		}


		$file_name = $this->cache_dir . "/" . $this->parent->getGridName() . time() . ".csv";
		$file = fopen($file_name, "w");
		foreach($export_columns as $column) {
			if($column instanceof Column\IColumn) {
				$header_arr[] = $column->getText();
			} else {
				if(is_array($column)) {
					$header_arr[] = reset($column);
				} else {
					$header_arr[] = $column;
				}
			}
		}
		fputcsv($file, $header_arr);

		$first = TRUE;
		foreach ($this->parent->getDataSource()->fetchAllForExport() as $data) {
			$line_data = array();
			foreach($export_columns as $column) {
				if($column instanceof Column\IColumn) {
					$line_data[] = $column->getBodyContent($data);
				} else {
					if(is_array($column)) {
						$column_name = key($column);
					} else {
						$column_name = $column;
					}
					if($first && !isset($data[$column_name])) {
						throw new Grid_Exception('Column "' . $column_name . '" does not exist in data.');
					}
					$line_data[] = $data[$column_name];
				}
			}
			fputcsv($file, $line_data);
			$first = FALSE;
		}
		fclose($file);

		$this->presenter->sendResponse( new \Nette\Application\Responses\FileResponse( $file_name , (is_null($this->file_name) ? $this->parent->getGridName() : $this->file_name) . '.csv' ) );
		unlink($file_name);
	}

}