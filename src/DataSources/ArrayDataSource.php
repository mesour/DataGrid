<?php

namespace DataGrid;

use \Mesour\ArrayManage\Searcher\Select;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class ArrayDataSource implements IDataSource {

	private $parent_key = 'parent_id';

	private $primary_key = 'id';

	/**
	 * @var Select
	 */
	private $select;

	/**
	 * @var Select
	 */
	private $export_select;

	/**
	 * @var Select
	 */
	private $full_select;

	/**
	 * Create instance
	 * 
	 * @param Array $data
	 */
	public function __construct(array $data) {
		$this->select = new Select($data);
		$this->export_select = clone $this->select;
		$this->full_select = clone $this->select;
	}

	/**
	 * Get array data count
	 * 
	 * @return Integer
	 */
	public function getTotalCount() {
		return $this->select->getTotalCount();
	}

	public function where($column, $value = NULL, $condition = NULL, $operator = 'and') {
		$this->select->where($column, $value, $condition, $operator);
		$this->export_select->where($column, $value, $condition, $operator);
	}

	/**
	 * Apply limit and offset
	 * 
	 * @param Integer $limit
	 * @param Integer $offset
	 */
	public function applyLimit($limit, $offset = 0) {
		$this->select->limit($limit);
		$this->select->offset($offset);
	}

	/**
	 * Get count after applied where
	 * 
	 * @return Integer
	 */
	public function count() {
		return $this->select->count();
	}

	/**
	 * Get searched values witp applied limit, offset and where
	 * 
	 * @return Array
	 */
	public function fetchAll() {
		return $this->select->fetchAll();
	}

	public function fetchAllForExport() {
		return $this->export_select->fetchAll();
	}

	public function fetchAssoc() {
		$output = array();
		foreach($this->fetchAll() as $value) {
			$output[$value[$this->parent_key]][] = $value;
		}
		return $output;
	}

	private function customFilter($how) {
		switch($how) {
			case 'equal_to';
				return \Mesour\ArrayManage\Searcher\Condition::EQUAL;
			case 'not_equal_to';
				return \Mesour\ArrayManage\Searcher\Condition::NOT_EQUAL;
			case 'bigger';
				return \Mesour\ArrayManage\Searcher\Condition::BIGGER;
			case 'not_bigger';
				return \Mesour\ArrayManage\Searcher\Condition::NOT_BIGGER;
			case 'smaller';
				return \Mesour\ArrayManage\Searcher\Condition::SMALLER;
			case 'not_smaller';
				return \Mesour\ArrayManage\Searcher\Condition::NOT_SMALLER;
			case 'start_with';
				return \Mesour\ArrayManage\Searcher\Condition::STARTS_WITH;
			case 'not_start_with';
				return \Mesour\ArrayManage\Searcher\Condition::NOT_STARTS_WITH;
			case 'end_with';
				return \Mesour\ArrayManage\Searcher\Condition::ENDS_WITH;
			case 'not_end_with';
				return \Mesour\ArrayManage\Searcher\Condition::NOT_ENDS_WITH;
			case 'equal';
				return \Mesour\ArrayManage\Searcher\Condition::CONTAINS;
			case 'not_equal';
				return \Mesour\ArrayManage\Searcher\Condition::NOT_CONTAINS;
			default:
				throw new Grid_Exception('Unexpected key for custom filtering.');
		}
	}

	public function applyCustom($column_name, array $custom, $type) {
		$values = array();

		if(!empty($custom['how1']) && !empty($custom['val1'])) {
			$values[] = $this->customFilter($custom['how1']);
		}
		if(!empty($custom['how2']) && !empty($custom['val2'])) {
			$values[] = $this->customFilter($custom['how2']);
		}
		if(count($values) === 2) {
			if($custom['operator'] === 'and') {
				$operator = 'and';
			} else {
				$operator = 'or';
			}
		}
		foreach($values as $key => $val) {
			$this->where($column_name, $custom['val'.($key+1)], $val, isset($operator) ? $operator : 'and');
		}
	}

	public function applyCheckers($column_name, array $value, $type) {
		foreach($value as $val) {
			$this->where($column_name, $val, \Mesour\ArrayManage\Searcher\Condition::EQUAL, 'or');
		}
	}

	public function fetchFullData($date_format = 'Y-m-d') {
		$output = array();
		foreach($this->full_select->fetchAll() as $data) {
			foreach($data as $key => $val) {
				if($val instanceof \DateTime) {
					$data[$key] = $val->format($date_format);
				}
			}
			$output[] = $data;
		}
		return $output;
	}

	public function orderBy($row, $sorting = 'ASC') {
		$this->select->orderBy($row, $sorting);
	}
	
	
	/**
	 * Return first element from data
	 * 
	 * @return Array
	 */
	public function fetch() {
		return $this->select->fetch();
	}

	public function getPrimaryKey() {
		return $this->primary_key;
	}

	public function setPrimaryKey($primary_key) {
		$this->primary_key = $primary_key;
	}

	public function getParentKey() {
		return $this->parent_key;
	}

	public function setParentKey($parent_key) {
		$this->parent_key = $parent_key;
	}

}
