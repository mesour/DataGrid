<?php

namespace Mesour\ArrayManage\Searcher;

use Mesour\ManagerException,
    Mesour\ArrayManage\Translator,
    Mesour\ArrayManage\Validator;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour ArrayManager
 */
class Select extends Search {

	private $data_array = array();

	private $column_array = array();

	private $total_count = 0;

	private $offset = 0;

	private $limit;

	private $ordering = array();

	private $validate_columns = FALSE;

	public function __construct(array $data_array) {
		parent::__construct($data_array);
		$this->data_array = $data_array;
		$this->total_count = count($data_array);
	}

	public function column($key) {
		$this->validate_columns = FALSE;
		foreach(func_get_args() as $arg) {
			$this->column_array[] = $arg;
		}
		return $this;
	}

	public function getTotalCount() {
		return $this->total_count;
	}

	public function limit($limit) {
		$this->limit = $limit;
		return $this;
	}

	public function offset($offset) {
		$this->offset = $offset;
		return $this;
	}

	public function count() {
		return count(array_slice($this->getResults(), $this->offset, $this->limit));
	}

	public function orderBy($row, $sorting = 'ASC') {
		// todo: add sorting by many columns
		Validator::validateUnknownColumns($this->data_array, array($row => NULL));
		if(empty($this->ordering)) {
			$this->ordering[$row] = $sorting;
		}
		return $this;
	}

	public function fetchAll() {
		$output = array();
		foreach ($this->getResults() as $key => $val) {
			$output[] = $this->applyColumns($val);
		}
		$this->applyOrdering($output);
		return array_slice($output, $this->offset, $this->limit);
	}

	public function fetch() {
		$output = $this->fetchAll();
		return reset($output);
	}

	public function fetchPairs($key, $value) {
		$output = array();
		foreach($this->fetchAll() as $val) {
			if(!isset($val[$key])) {
				throw new ManagerException('Column ' . $key . ' does not exist in array.');
			}
			if(!isset($val[$value])) {
				throw new ManagerException('Column ' . $value . ' does not exist in array.');
			}
			$output[$val[$key]] = $val[$value];
		}
		return $output;
	}

	public function test() {
		echo '<pre>';
		$select = (new Translator(Translator::SELECT, $this->column_array, $this->limit, $this->offset, $this->ordering))->translate();
		echo str_replace('{WHERE}', $this->translate(), $select);
		echo '</pre>';
	}

	private function applyOrdering(& $output) {
		if(empty($this->ordering)) {
			return;
		}
		$arguments = array();
		foreach ($output as $rec) {
			$x = 0;
			foreach($this->ordering as $key => $sorting) {
				$arguments[$x][] = is_numeric($rec[$key]) ? $rec[$key] : strtolower($rec[$key]);
				$arguments[$x+1] = $sorting === 'ASC' ? SORT_ASC : SORT_DESC;
				$x = $x+2;
			}
		}
		$x = 0;
		foreach($this->ordering as $key => $sorting) {
			array_multisort($arguments[$x], $arguments[$x+1], $output);
			$x = $x+2;
		}
	}

	private function applyColumns($data) {
		if(!$this->validate_columns) {
			$arr = array_flip($this->column_array);
			unset($arr['*']);
			Validator::validateUnknownColumns($this->data_array, $arr);
			$this->validate_columns = TRUE;
		}
		if(empty($this->column_array) || array_search('*', $this->column_array) !== FALSE) {
			return $data;
		} else {
			$current_data = array();
			foreach($this->column_array as $key) {
				$current_data[$key] = $data[$key];
			}
			return $current_data;
		}
	}

}