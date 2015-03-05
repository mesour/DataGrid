<?php

namespace Mesour\DataGrid;

use \Nette\Database\Table\Selection,
    \Nette\Utils;

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
	 * @var array
	 */
	private $where_arr = array();

	/**
	 * @var integer
	 */
	private $limit;

	/**
	 * @var integer
	 */
	private $offset = 0;

	private $total_count = 0;

	/**
	 * Create instance
	 *
	 * @param \Nette\Database\Table\Selection $nette_table
	 */
	public function __construct(Selection $nette_table) {
		$this->nette_table = $nette_table;
		$this->total_count = $nette_table->count('*');
	}

	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getTableSelection() {
		return $this->getSelection();
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
		$this->where_arr[] = func_get_args();
	}

	/**
	 * Apply limit and offset
	 *
	 * @param Integer $limit
	 * @param Integer $offset
	 */
	public function applyLimit($limit, $offset = 0) {
		$this->limit = $limit;
		$this->offset = $offset;
	}

	/**
	 * Get count after applied where
	 *
	 * @return Integer
	 */
	public function count() {
		$count = $this->getSelection()->count('*');
		$to_end = $count - ($this->offset + $this->limit);
		return !is_null($this->limit) && $this->limit < $count ? ($to_end < $this->limit ? $to_end : $this->limit) : $count;
	}

	private function getSelection($limit = TRUE, $where = TRUE) {
		$selection = clone $this->nette_table;
		if ($where) {
			foreach ($this->where_arr as $conditions) {
				call_user_func_array(array($selection, 'where'), $conditions);
			}
		}
		if ($limit) {
			$selection->limit($this->limit, $this->offset);
		}
		return $selection;
	}

	/**
	 * Get searched values with applied limit, offset and where
	 *
	 * @return array
	 */
	public function fetchAll() {
		$output = array();
		$selection = $this->getSelection();
		foreach ($selection as $data) {
			$output[] = $data->toArray();
		}
		return $output;
	}

	public function fetchAllForExport() {
		$output = array();
		$selection = $this->getSelection(FALSE);
		foreach ($selection as $data) {
			$output[] = $data->toArray();
		}
		return $output;
	}

	private function customFilter($column_name, $how, $value, $type) {
		$output = array();
		$column_name = $type === 'date' ? ('DATE(' . $column_name . ')') : $column_name;
		switch ($how) {
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
		if (!empty($custom['how1']) && !empty($custom['val1'])) {
			$values[] = $this->customFilter($column_name, $custom['how1'], $custom['val1'], $type);
		}
		if (!empty($custom['how2']) && !empty($custom['val2'])) {
			$values[] = $this->customFilter($column_name, $custom['how2'], $custom['val2'], $type);
		}
		if (count($values) === 2) {
			if ($custom['operator'] === 'and') {
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
		if ($type === 'date') {
			$is_timestamp = TRUE;
			foreach ($value as $val) {
				if (!is_numeric($val)) {
					$is_timestamp = FALSE;
					break;
				}
			}
			if ($is_timestamp) {
				$where = '(';
				$i = 1;
				foreach ($value as $val) {
					$where .= '(' . $column_name . ' >= ' . (int)$val . ' AND ' . $column_name . ' <= ' . (((int)$val) + 86398) . ')';
					if ($i < count($value)) {
						$where .= ' OR ';
					}
					$i++;
				}
				$where .= ')';
				$this->where($where);
			} else {
				$this->where('DATE(' . $column_name . ')', $value);
			}
		} else {
			$this->where($column_name, $value);
		}
	}

	public function fetchFullData($date_format = 'Y-m-d') {
		$output = array();
		$selection = $this->getSelection(FALSE, FALSE);
		foreach ($selection as $data) {
			$current_data = $data->toArray();
			foreach ($current_data as $key => $val) {
				if ($val instanceof Utils\DateTime) {
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
		foreach ($data as $row) {
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
		if ($this->total_count > 0) {
			return $this->getSelection(FALSE, FALSE)->limit(1, 0)->fetch()->toArray();
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