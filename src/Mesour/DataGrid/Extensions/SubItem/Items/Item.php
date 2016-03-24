<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\SubItem\Items;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
abstract class Item extends Mesour\Object
{

	const DEFAULT_COUNT = 20;

	/** @var Mesour\DataGrid\Extensions\SubItem\ISubItem */
	protected $parent;

	protected $callback;

	protected $checkCallback;

	protected $type;

	protected $name;

	protected $disabled = false;

	protected $description;

	protected $pageLimit;

	protected $keys = [];

	protected $permission = false;

	protected $aliases = [];

	public function __construct(Mesour\DataGrid\Extensions\SubItem\ISubItem $parent, $name, $description = null)
	{
		$this->parent = $parent;
		$this->name = $name;
		$this->pageLimit = $this->parent->getPageLimit();
		$this->description = $description;
	}

	/**
	 * @return Mesour\DataGrid\SubItemGrid
	 */
	protected function getGrid()
	{
		return $this->getParent()->getParent();
	}

	public function setCallback($callback)
	{
		Mesour\Components\Utils\Helpers::checkCallback($callback);
		$this->callback = $callback;
		return $this;
	}

	public function setCheckCallback($callback)
	{
		Mesour\Components\Utils\Helpers::checkCallback($callback);
		$this->checkCallback = $callback;
		return $this;
	}

	public function check($rowData)
	{
		if ($this->checkCallback) {
			Mesour\Components\Utils\Helpers::invokeArgs($this->checkCallback, [$rowData, $this]);
		}
	}

	public function isDisabled()
	{
		return $this->disabled;
	}

	public function setDisabled($disabled = true)
	{
		$this->disabled = $disabled;
		return $this;
	}

	public function setPermission($resource, $privilege)
	{
		$this->permission = [$this->parent->getUserRole(), $resource, $privilege];
		return $this;
	}

	public function isAllowed()
	{
		return !$this->permission
		|| Mesour\Components\Utils\Helpers::invokeArgs([$this->parent->getAuthorizator(), 'isAllowed'], $this->permission);
	}

	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return Mesour\DataGrid\Column\SubItem
	 */
	protected function getParent()
	{
		return $this->parent;
	}

	public function setDescription($description)
	{
		return $this->description = $description;
	}

	public function getDescription()
	{
		return $this->description ? $this->description : $this->name;
	}

	public function addAlias($forKey, $alias)
	{
		$this->aliases[$forKey] = $alias;
	}

	public function getTranslatedKey($key)
	{
		return isset($this->aliases[$key]) ? $this->aliases[$key] : $key;
	}

	public function invoke(array $args = [], $name, $key)
	{
		return $this->callback ? Mesour\Components\Utils\Helpers::invokeArgs($this->callback, $args) : null;
	}

	public function hasKey($key)
	{
		return isset($this->parent[$this->name . $key]);
	}

	public function getKeys()
	{
		return $this->keys;
	}

	abstract public function render();

	abstract public function reset();

}
