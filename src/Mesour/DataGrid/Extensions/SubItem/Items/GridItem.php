<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\SubItem\Items;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class GridItem extends Item
{

	public function __construct(
		Mesour\DataGrid\Extensions\SubItem\ISubItem $parent,
		$name,
		$description = null,
		Mesour\UI\DataGrid $grid = null
	)
	{
		parent::__construct($parent, $name, $description);
		$i = 0;
		while ($i < (is_null($this->pageLimit) ? self::DEFAULT_COUNT : $this->pageLimit)) {
			$currentGrid = clone $grid;
			$currentGrid->setName($name . $i);
			$this->parent->addComponent($currentGrid, $name . $i);
			$this->keys[] = $i;
			$i++;
		}
	}

	public function render($key = null)
	{
		if (is_null($key)) {
			return '';
		}
		/** @var Mesour\UI\DataGrid $grid */
		$grid = $this->parent->getComponent($this->name . $this->getTranslatedKey($key));
		return $grid->create();
	}

	public function reset()
	{
		$i = 0;
		while ($i <= (is_null($this->pageLimit) ? self::DEFAULT_COUNT : $this->pageLimit)) {
			if (isset($this->parent[$this->name . $i])) {
				$this->parent[$this->name . $i]->reset(true);
			}
			$i++;
		}
	}

	public function invoke(array $args = [], $name, $key)
	{
		$arguments = [$this->parent->getComponent($name . $key)];
		$arguments = array_merge($arguments, $args);
		return parent::invoke($arguments, $name, $key);
	}

}
