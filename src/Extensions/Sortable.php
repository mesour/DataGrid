<?php

namespace Mesour\DataGrid\Extensions;

use Mesour\DataGrid\Grid_Exception;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Sortable extends BaseControl {

	/**
	 * @persistent Array
	 */
	public $sortable_data = array();

	/**
	 * @throws Grid_Exception
	 */
	public function handleSortData() {
		$params = array();
		$item_id = $this->sortable_data['item'];
		parse_str($this->sortable_data['serialized'], $params);
		$data = $params[$this->parent->getName()];
		foreach ($data as $key => $val) {
			if ($val === 'null') {
				$data[$key] = NULL;
			}
		}
		if (!is_array($data)) {
			throw new Grid_Exception('Empty post data from column sorting.');
		}
		$this->parent->onSort($data, $item_id);
		$this->parent->reset();
		$this->parent->redrawControl();
		$this->presenter->redrawControl();
	}

}