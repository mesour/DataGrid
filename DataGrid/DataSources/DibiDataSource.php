<?php

namespace DataGrid;

/**
 * Dibi data source for data grid
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class DibiDataSource implements IDataSource {
	
	private $parent_key = 'parent_id';
	
	private $primary_key = 'id';

	/**
	 * Contains dibi data source instance
	 *
	 * @var \DibiDataSource
	 */
	private $dibi_data_source = array();

	/**
	 * Create instance
	 * 
	 * @param \DibiDataSource $data_source
	 */
	public function __construct(\DibiDataSource $data_source) {
		$this->dibi_data_source = $data_source;
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
		return $this->dibi_data_source->fetchAssoc($this->parent_key.',#');
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
		if($row = $this->dibi_data_source->fetch()) {
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
