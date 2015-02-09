<?php

namespace Mesour\DataGrid\Extensions;

use Mesour\DataGrid\BasicGrid;
use Nette\ComponentModel\IComponent;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class GridItem extends Item {

	public function __construct(IComponent $parent, $name, $description = NULL, BasicGrid $grid = NULL) {
		parent::__construct($parent, $name, $description);
		$i = 0;
		while ($i <= (is_null($this->page_limit) ? self::DEFAULT_COUNT : $this->page_limit)) {
			$_grid = clone $grid;
			$_grid->setName($name . $i);
			$this->parent->getParent()->addComponent($_grid, $name . $i);
			$this->keys[] = $i;
			$i++;
		}
	}

	public function render($key = NULL) {
		if (is_null($key)) {
			return '';
		}
		$grid = $this->parent->parent[$this->name . $this->getTranslatedKey($key)];
		return $grid->render(TRUE);
	}

	public function reset() {
		$i = 0;
		while ($i <= (is_null($this->page_limit) ? self::DEFAULT_COUNT : $this->page_limit)) {
			$this->parent->parent[$this->name . $i]->reset(TRUE);
			$i++;
		}
	}

	public function invoke(array $args = array(), $name, $key) {
		$arguments = array($this->parent->parent[$name . $key]);
		$arguments = array_merge($arguments, $args);
		return parent::invoke($arguments, $name, $key);
	}


}