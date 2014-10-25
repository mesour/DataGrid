<?php

namespace DataGrid\Extensions;

use DataGrid\Grid_Exception;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Editable extends BaseControl {

	/**
	 * @persistent Array
	 */
	public $editable_data = array();

	/**
	 * @throws Grid_Exception
	 */
	public function handleEditCell() {
		$data = $this->editable_data['data'];
		if (!is_array($data)) {
			throw new Grid_Exception('Empty request from column edit.');
		}
		$has_permission = FALSE;
		foreach ($this->parent->getColumns() as $column) {
			if ($column->getId() === $data['columnName'] && $column->isEditable()) {
				$has_permission = TRUE;
			}
		}
		if ($has_permission) {
			$this->parent->onEditCell($data['lineId'], $data['columnName'], $data['newValue'], $data['oldValue']);
			$this->parent->redrawControl();
			$this->presenter->redrawControl();
		} else {
			throw new Grid_Exception('Column with ID ' . $data['columnName'] . ' is not editable or does not exists in DataGrid columns.');
		}
	}

}