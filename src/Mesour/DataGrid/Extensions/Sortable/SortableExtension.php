<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\Sortable;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class SortableExtension extends Mesour\DataGrid\Extensions\Base implements ISortable
{

	private $sortableUsed = false;

	private $columnName = false;

	public function getSpecialColumn()
	{
		$this->sortableUsed = true;
		$this->getGrid()->setAttribute('data-mesour-sortable', $this->createLinkName());
		return new Mesour\DataGrid\Column\Sortable;
	}

	public function setColumnName($columnName)
	{
		$this->columnName = $columnName;
		return $this;
	}

	public function getSpecialColumnName()
	{
		return $this->columnName;
	}

	public function setPermission($resource, $privilege)
	{
		$this->setPermissionCheck($resource, $privilege);
		return $this;
	}

	public function isGetSpecialColumnUsed()
	{
		return $this->sortableUsed;
	}

	public function handleSortData($data, $item)
	{
		if ($this->isDisabled()) {
			throw new Mesour\InvalidStateException('Cannot sort data if extension is disabled.');
		}
		if (!$this->isAllowed()) {
			throw new Mesour\InvalidStateException('Invalid permissions.');
		}
		$params = [];
		$itemId = $item;
		parse_str($data, $params);
		$data = $params[$this->getGrid()->createLinkName()];
		foreach ($data as $key => $val) {
			if ($val === 'null') {
				$data[$key] = null;
			}
		}
		if (!is_array($data)) {
			throw new Mesour\InvalidStateException('Empty post data from column sorting.');
		}
		$this->getGrid()->reset(true);
		$this->getGrid()->onSort($data, $itemId);
	}

	public function gridCreate($data = [])
	{
		$this->create();
	}

}
