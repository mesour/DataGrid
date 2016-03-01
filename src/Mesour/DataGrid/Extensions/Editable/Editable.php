<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\Editable;

use Mesour;
use Mesour\DataGrid\Extensions;


/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class EditableExtension extends Extensions\Base implements IEditable
{

	/**
	 * @persistent Array
	 */
	public $editable_data = [];

	public function setPermission($resource, $privilege)
	{
		$this->setPermissionCheck($resource, $privilege);
		return $this;
	}

	public function attached(Mesour\Components\ComponentModel\IContainer $parent)
	{
		parent::attached($parent);
		/** @var Mesour\UI\DataGrid $parent */
		$parent->setAttribute('data-mesour-editable', $this->createLinkName());
	}

	/**
	 * @param array $data
	 * @throws Mesour\InvalidStateException
	 * @throws Mesour\Components\BadRequestException
	 */
	public function handleEditCell(array $data)
	{
		if ($this->isDisabled()) {
			throw new Mesour\InvalidStateException('Cannot edit cell if extension is disabled.');
		}
		if (!$this->isAllowed()) {
			throw new Mesour\InvalidStateException('Invalid permissions.');
		}
		if (!is_array($data)) {
			throw new Mesour\Components\BadRequestException('Empty request from column edit.');
		}

		if ($this->checkPermission($this->getGrid()->getColumns(), $data['columnName'])) {
			$this->getGrid()->onEditCell($data['lineId'], $data['columnName'], $data['newValue'], $data['oldValue'], isset($data['dataValue']) ? $data['dataValue'] : null);
		} else {
			throw new Mesour\InvalidStateException('Column with ID ' . $data['columnName'] . ' is not editable or does not exists in DataGrid columns.');
		}
	}

	private function checkPermission($columns, $columnName)
	{
		foreach ($columns as $column) {
			if ($column instanceof Mesour\DataGrid\Column\IContainer) {
				$perm = $this->checkPermission($column, $columnName);
				if ($perm) {
					return true;
				}
			} else {
				if ($this->checkPermissionHelper($column, $columnName)) {
					return true;
				}
			}
		}
		return false;
	}

	private function checkPermissionHelper($column, $columnName)
	{
		return $column instanceof Mesour\DataGrid\Column\IInlineEdit && $column->getName() === $columnName && $column->hasEditable();
	}

	public function gridCreate($data = [])
	{
		$this->create();
	}

}