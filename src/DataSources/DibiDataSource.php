<?php

namespace Mesour\DataGrid;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class DibiDataSource implements IDataSource {

	private $parent_key = 'parent_id';

	private $primary_key = 'id';

	/**
	 * @var \DibiDataSource
	 */
	private $dibi_data_source = array();

	/**
	 * @var \DibiDataSource
	 */
	private $dibi_data_source_full = array();

	/**
	 * Create instance
	 *
	 * @param \DibiDataSource $data_source
	 */
	public function __construct(\DibiDataSource $data_source) {
		$this->dibi_data_source_full = clone $data_source;
		$this->dibi_data_source = $data_source;
	}

	/**
	 * @return \DibiDataSource
	 */
	public function getDibiDataSource() {
		return $this->dibi_data_source;
	}

	/**
	 * Get array data count
	 *
	 * @return Integer
	 */
	public function getTotalCount() {
		return $this->dibi_data_source->getTotalCount();
	}

	/**
	 * Add where condition
	 *
	 * @param Mixed $args Dibi args
	 */
	public function where($args) {
		call_user_func_array(array($this->dibi_data_source, 'where'), func_get_args());
	}

	/**
	 * Apply limit and offset
	 *
	 * @param Integer $limit
	 * @param Integer $offset
	 */
	public function applyLimit($limit, $offset = 0) {
		$this->dibi_data_source->applyLimit($limit, $offset);
	}

	/**
	 * Get count after applied where
	 *
	 * @return Integer
	 */
	public function count() {
		return $this->dibi_data_source->count();
	}

	/**
	 * Get searched values witp applied limit, offset and where
	 *
	 * @return \DibiRow
	 */
	public function fetchAll() {
		return $this->dibi_data_source->fetchAll();
	}

	public function fetchAssoc() {
		return $this->dibi_data_source->fetchAssoc($this->parent_key . ',#');
	}

	public function fetchAllForExport() {
		$this->applyLimit(1000000, 0);
		return $this->dibi_data_source->fetchAll();
	}

	private function customFilter($column_name, $how, $type) {
		$column_name = $type === 'date' ? ('DATE([' . $column_name . '])') : ('[' . $column_name . ']');
		switch ($how) {
			case 'equal_to';
				return $column_name . ' = ?';
			case 'not_equal_to';
				return $column_name . ' != ?';
			case 'bigger';
				return $column_name . ' > ?';
			case 'not_bigger';
				return $column_name . ' <= ?';
			case 'smaller';
				return $column_name . ' < ?';
			case 'not_smaller';
				return $column_name . ' >= ?';
			case 'start_with';
				return $column_name . ' LIKE %like~';
			case 'not_start_with';
				return $column_name . ' NOT LIKE %like~';
			case 'end_with';
				return $column_name . ' LIKE %~like';
			case 'not_end_with';
				return $column_name . ' NOT LIKE %~like';
			case 'equal';
				return $column_name . ' LIKE %~like~';
			case 'not_equal';
				return $column_name . ' NOT LIKE %~like~';
			default:
				throw new Grid_Exception('Unexpected key for custom filtering.');
		}
		return $output;
	}

	public function applyCustom($column_name, array $custom, $type) {
		$parameters = array('(');
		if (!empty($custom['how1']) && !empty($custom['val1'])) {
			$parameters[] = $this->customFilter($column_name, $custom['how1'], $type);
			$parameters[] = $custom['val1'];
		}
		if (!empty($custom['how2']) && !empty($custom['val2'])) {
			if ($custom['operator'] === 'and') {
				$parameters[] = 'AND';
			} else {
				$parameters[] = 'OR';
			}
			$parameters[] = $this->customFilter($column_name, $custom['how2'], $type);
			$parameters[] = $custom['val2'];
		}
		$parameters[] = ')';

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
				$this->where('DATE([' . $column_name . ']) IN %in', $value);
			}
		} else {
			$this->where('[' . $column_name . '] IN %in', $value);
		}
	}

	public function fetchFullData($date_format = 'Y-m-d') {
		$output = array();
		foreach ($this->dibi_data_source_full->fetchAll() as $data) {
			$current_data = $data->toArray();
			foreach ($current_data as $key => $val) {
				if ($val instanceof \DibiDateTime) {
					$current_data[$key] = $val->format($date_format);
				}
			}
			$output[] = $current_data;
		}
		return $output;
	}

	public function orderBy($row, $sorting = 'ASC') {
		return $this->dibi_data_source->orderBy($row, $sorting);
	}

	/**
	 * Return first element from data
	 *
	 * @return Array
	 */
	public function fetch() {
		if ($row = $this->dibi_data_source->fetch()) {
			return $row->toArray();
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
