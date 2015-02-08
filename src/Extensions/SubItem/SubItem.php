<?php

namespace Mesour\DataGrid\Extensions;

use Mesour\DataGrid\BasicGrid;
use Mesour\DataGrid\Grid_Exception;
use Nette\ComponentModel\IContainer;
use Nette\Utils\Callback;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class SubItem extends BaseControl {

	private $items = array();

	private $page_limit;

	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		$this->page_limit = $parent->getPageLimit();
	}

	/**
	 * @param $name
	 * @param BasicGrid $grid
	 * @param null|string $description
	 * @return GridItem
	 */
	public function addGrid($name, BasicGrid $grid, $description = NULL) {
		$item = new GridItem($this, $name, $description, $grid);
		$this->items[$name] = $item;
		return $item;
	}

	public function reset() {
		$this->getSession()->settings = $this->settings = array();
		foreach($this->items as $item) {
			$item->reset();
		}
	}

	public function getOpened() {
		$output = array();
		foreach ($this->settings as $name => $value) {
			if (!isset($this->items[$name])) {
				unset($this->settings[$name]);
				continue;
			}
			$keys = $this->items[$name]->getKeys();
			if(count($keys) < count($this->settings[$name])) {
				while(count($keys) < count($this->settings[$name])) {
					array_shift($this->settings[$name]);
				}
			}
			foreach($this->settings[$name] as $key => $i) {
				if (!isset($this->settings[$name][$key])) {
					unset($this->settings[$name][$key]);
					continue;
				}
				if(!$this->items[$name]->hasKey($this->settings[$name][$key])) {

					$this->items[$name]->addAlias($i, end($keys));
				}
				$output[$name]['keys'][] = $this->settings[$name][$key];
				if (!isset($output[$name]['item'])) {
					$output[$name]['item'] = $this->items[$name];
				}
			}
		}

		$this->getSession()->settings = $this->settings;
		return $output;
	}

	public function getItem($name) {
		if (!isset($this->items[$name])) {
			throw new Grid_Exception('Item ' . $name . ' does not exist.');
		}
		return $this->items[$name];
	}

	public function getNames() {
		return array_keys($this->items);
	}

	public function getItemsCount() {
		return count($this->items);
	}

	public function hasSubItems() {
		return count($this->items) > 0;
	}

	public function handleToggleItem($key, $name) {

		if (isset($this->settings[$name])) {
			if (in_array($key, $this->settings[$name])) {
				unset($this->settings[$name][array_search($key, $this->settings[$name])]);
			} else {
				$this->settings[$name][] = $key;
			}
		} else {
			$this->settings[$name][] = $key;
		}
		$this->getSession()->settings = $this->settings;
		$this->parent->redrawControl();
		if ($this->parent->isSubGrid()) {
			$this->parent->parent->redrawControl();
		}
		$this->presenter->redrawControl();
	}

}