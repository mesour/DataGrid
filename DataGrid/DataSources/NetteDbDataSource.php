<?php

namespace DataGrid;

/**
 * Nette database data source for data grid
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class NetteDbDataSource implements IDataSource {
	
	private $parent_key = 'parent_id';
	
	private $primary_key = 'id';

	/**
	 * Contains dibi data source instance
	 *
	 * @var \Nette\Database\Table\Selection
	 */
	private $nette_table = array();

	/**
	 * Contains dibi data source instance
	 *
	 * @var \Nette\Database\Table\Selection
	 */
	private $nette_original_table = array();

	private $total_count = 0;

	/**
	 * Create instance
	 * 
	 * @param \Nette\Database\Table\Selection $nette_table
	 */
	public function __construct(\Nette\Database\Table\Selection $nette_table) {
		$this->nette_original_table = $nette_table;
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