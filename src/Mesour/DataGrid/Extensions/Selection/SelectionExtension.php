<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\Selection;

use Mesour;


/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class SelectionExtension extends Mesour\UI\Selection implements ISelection
{

	private $selection_used = false;

	private $disabled = false;

	/** @var Mesour\UI\DropDown */
	private $links;

	public function __construct($name = null, Mesour\Components\ComponentModel\IContainer $parent = null)
	{
		parent::__construct($name, $parent);
		$this->links = new Links($this);
	}

	/**
	 * @return Links
	 */
	public function getLinks()
	{
		return $this->links;
	}

	public function getSpecialColumn()
	{
		$this->selection_used = true;
		return new Mesour\DataGrid\Column\Selection;
	}

	public function getSpecialColumnName()
	{
		return '_grid_selection';
	}

	public function isGetSpecialColumnUsed()
	{
		return $this->selection_used;
	}

	/**
	 * @return bool
	 */
	public function isDisabled()
	{
		return $this->disabled;
	}

	public function setDisabled($disabled = true)
	{
		$this->disabled = (bool)$disabled;
		return $this;
	}

	public function handleOnSelect($name, array $items)
	{
		$link = $this->getLinks()->getLink($name);
		if (!$link->isAllowed()) {
			throw new Mesour\InvalidStateException('Invalid permissions.');
		}
		$link->onCall($items);
	}

	public function gridCreate($data = [])
	{
	}

	public function createInstance(Mesour\DataGrid\Extensions\IExtension $extension, $name = null)
	{
	}

	public function afterGetCount($count)
	{
	}

	public function beforeFetchData($data = [])
	{
	}

	/**
	 * @return Mesour\DataGrid\ExtendedGrid
	 */
	public function getGrid()
	{
		return $this->getParent();
	}

	public function afterFetchData($currentData, $data = [], $rawData = [])
	{
		$items = [];
		$statuses = [];
		foreach ($this->getGrid()->getColumns() as $column) {
			if ($column instanceof Mesour\DataGrid\Column\Status) {
				foreach ($currentData as $item) {
					$_item = null;
					foreach ($column as $statusItem) {
						/** @var Mesour\DataGrid\Column\Status\IStatusItem $statusItem */
						$options = $statusItem->getStatusOptions();
						if ($options) {
							if ($item[$column->getName()] == $statusItem->getStatus()) {
								$_item = $options;
								break;
							}
						}
					}
					if (!is_null($_item)) {
						$statusName = reset($_item);
						$_key = key($_item);
						$statuses[$_key] = $statusName;
						$items[$item[$this->getGrid()->getPrimaryKey()]] = (string)$_key;
					}
				}
			}
		}
		$this->setItems($items);
		foreach ($statuses as $status => $statusName) {
			$this->addStatus($status, $statusName);
		}
	}

	public function attachToRenderer(Mesour\DataGrid\Renderer\IGridRenderer $renderer, $data = [], $rawData = [])
	{
		$renderer->setComponent('selection', $this->getLinks()->create($data));
	}

	public function reset($hard = false)
	{
	}

	public function __clone()
	{
		parent::__clone();
		$this->links = clone $this->links;
		$this->links->setParent($this);
	}

}