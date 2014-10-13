<?php

namespace DataGrid;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class NetteDbDataSource implements IDataSource {
	
	private $parent_key = 'parent_id';
	
	private $primary_key = 'id';

	/**
	 * @var \Nette\Database\Table\Selection
	 */
	private $nette_table;

	/**
	 * @var \Nette\Database\Table\Selection
	 */
	private $nette_original_table;

	/**
	 * @var \Nette\Database\Table\Selection
	 */
	private $nette_full_table;

	private $total_count = 0;

	/**
	 * Create instance
	 * 
	 * @param \Nette\Database\Table\Selection $nette_table
	 */
	public function __construct(\Nette\Database\Table\Selection $nette_table) {
		$this->nette_original_table = $nette_table;
		$this->nette_full_table = clone $nette_table;
		$this->nette_table = clone $nette_table;
		$this->total_count = $this->nette_original_table->count();
	}

	/**
	 * Get total count of source data
	 * 
	 * @return Integer
	 */
	public function getTotalCount() {
		return $this->total_count;
	}

	/**
	 * Add where condition
	 * 
	 * @param Mixed $args NetteDatabase args
	 */
	public function where($args) {
		call_user_func_array(array($this->nette_table, 'where'), func_get_args());
		call_user_func_array(array($this->nette_original_table, 'where'), func_get_args());
	}

	/**
	 * Apply limit and offset
	 * 
	 * @param Integer $limit
	 * @param Integer $offset
	 */
	public function applyLimit($limit, $offset = 0) {
		$this->nette_table->limit($limit, $offset);
	}

	/**
	 * Get count after applied where
	 * 
	 * @return Integer
	 */
	public function count() {
		return $this->nette_original_table->count();
	}

	/**
	 * Get searched values with applied limit, offset and where
	 * 
	 * @return \DibiRow
	 */
	public function fetchAll() {
		$output = array();
		foreach($this->nette_table->fetchAll() as $data) {
			$output[] = $data->toArray();
		}
		return $output;
	}

	public function fetchAllForExport() {
		$output = array();
		foreach($this->nette_original_table->fetchAll() as $data) {
			$output[] = $data->toArray();
		}
		return $output;
	}

	private function customFilter($column_name, $how, $value, $type) {
		$output = array();
		$column_name = $type === 'date' ? ('DATE(' . $column_name . ')') : $column_name;
		switch($how) {
			case 'equal_to';
				$output[] = $column_name . ' = ?';
				$output[] = $value;
				break;
			case 'not_equal_to';
				$output[] = $column_name . ' != ?';
				$output[] = $value;
				break;
			case 'bigger';
				$output[] = $column_name . ' > ?';
				$output[] = $value;
				break;
			case 'not_bigger';
				$output[] = $column_name . ' <= ?';
				$output[] = $value;
				break;
			case 'smaller';
				$output[] = $column_name . ' < ?';
				$output[] = $value;
				break;
			case 'not_smaller';
				$output[] = $column_name . ' >= ?';
				$output[] = $value;
				break;
			case 'start_with';
				$output[] = $column_name . ' LIKE ?';
				$output[] = $value . '%';
				break;
			case 'not_start_with';
				$output[] = $column_name . ' NOT LIKE ?';
				$output[] = $value . '%';
				break;
			case 'end_with';
				$output[] = $column_name . ' LIKE ?';
				$output[] = '%' . $value;
				break;
			case 'not_end_with';
				$output[] = $column_name . ' NOT LIKE ?';
				$output[] = '%' . $value;
				break;
			case 'equal';
				$output[] = $column_name . ' LIKE ?';
				$output[] = '%' . $value . '%';
				break;
			case 'not_equal';
				$output[] = $column_name . ' NOT LIKE ?';
				$output[] = '%' . $value . '%';
				break;
			default:
				throw new Grid_Exception('Unexpected key for custom filtering.');
		}
		return $output;
	}

	public function applyCustom($column_name, array $custom, $type) {
		$values = array();
		if(!empty($custom['how1']) && !empty($custom['val1'])) {
			$values[] = $this->customFilter($column_name, $custom['how1'], $custom['val1'], $type);
		}
		if(!empty($custom['how2']) && !empty($custom['val2'])) {
			$values[] = $this->customFilter($column_name, $custom['how2'], $custom['val2'], $type);
		}
		if(count($values) === 2) {
			if($custom['operator'] === 'and') {
				$operator = 'AND';
			} else {
				$operator = 'OR';
			}
			$parameters = array('(' . $values[0][0] . ' ' . $operator . ' ' . $values[1][0] . ')', $values[0][1], $values[1][1]);
		} else {
			$parameters = array($values[0][0], $values[0][1]);
		}
		call_user_func_array(array($this, 'where'), $parameters);
	}

	public function applyCheckers($column_name, array $value, $type) {
		$this->where($type === 'date' ? 'DATE('.$column_name.')' : $column_name, $value);
	}

	public function fetchFullData($date_format = 'Y-m-d') {
		$output = array();
		foreach($this->nette_full_table->fetchAll() as $data) {
			$current_data = $data->toArray();
			foreach($current_data as $key => $val) {
				if($val instanceof \Nette\Utils\DateTime) {
					$current_data[$key] = $val->format($date_format);
				}
			}
			$output[] = $current_data;
		}
		return $output;
	}

	public function fetchAssoc() {
		$data = $this->fetchAll();
		$output = array();
		foreach($data as $row) {
			$output[$row[$this->parent_key]][] = $row;
		}
		return $output;
	}

	public function orderBy($row, $sorting = 'ASC') {
		return $this->nette_table->order($row . ' ' . $sorting);
	}
	
	/**
	 * Return first element from data
	 * 
	 * @return Array
	 */
	public function fetch() {
		if($this->nette_table->fetch()) {
			$table = clone $this->nette_table;
			return $table->limit(1, 0)->fetch()->toArray();
		} else {
			return array();
		}	
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