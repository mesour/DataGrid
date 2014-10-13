<?php

namespace DataGrid\Extensions;

use DataGrid\Grid_Exception;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Sortable extends BaseControl {

	/**
	 * @persistent
	 */
	public $sortable_data;

	/**
	 * @throws Grid_Exception
	 */
	public function handleSortData() {
		$params = array();
		parse_str($this->sortable_data, $params);
		$data = $params[$this->parent->getLineIdName()];
		if (!is_array($data)) {
			throw new Grid_Exception('Empty post data from column sorting.');
		}
		$this->parent->onSort($data);
	}

}