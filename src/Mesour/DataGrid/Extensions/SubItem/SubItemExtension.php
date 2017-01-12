<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\SubItem;

use Mesour;
use Mesour\DataGrid\Extensions;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class SubItemExtension extends Extensions\Base implements ISubItem
{

	private $items = [];

	/** @var Mesour\Components\Session\ISessionSection */
	private $privateSession;

	public function attached(Mesour\Components\ComponentModel\IContainer $parent)
	{
		parent::attached($parent);
		$this->privateSession = $this->getSession()->getSection($this->createLinkName());
		return $this;
	}

	public function getPageLimit()
	{
		$parent = $this->getGrid();
		$pager = $parent->getExtension('IPager', false);
		if ($pager instanceof Extensions\Pager\IPager) {
			return $pager->getPaginator()->getItemsPerPage();
		}
		return $parent->getSource()->getTotalCount();
	}

	/**
	 * @param string $name
	 * @param null|string $description
	 * @param Mesour\UI\DataGrid $grid
	 * @return Extensions\SubItem\Items\GridItem
	 */
	public function addGridItem($name, $description, Mesour\UI\DataGrid $grid)
	{
		$this->check($name);
		$item = new Extensions\SubItem\Items\GridItem($this, $name, $this->getTranslator()->translate($description), $grid);
		$this->items[$name] = $item;
		return $item;
	}

	public function addTemplateItem($name, $description)
	{
		$this->check($name);
		$item = new Extensions\SubItem\Items\TemplateItem($this, $name, $this->getTranslator()->translate($description));
		$this->items[$name] = $item;
		return $item;
	}

	public function addComponentItem($name, $description, $callback)
	{
		if (!$callback instanceof Mesour\Components\Control\IControl) {
			Mesour\Components\Utils\Helpers::checkCallback($callback);
		}
		$this->check($name);
		$item = new Extensions\SubItem\Items\ComponentItem($this, $name, $this->getTranslator()->translate($description), $callback);
		$this->items[$name] = $item;
		return $item;
	}

	public function addCallbackItem($name, $description)
	{
		$this->check($name);
		$item = new Extensions\SubItem\Items\CallbackItem($this, $name, $this->getTranslator()->translate($description));
		$this->items[$name] = $item;
		return $item;
	}

	public function setPermission($resource, $privilege)
	{
		$this->setPermissionCheck($resource, $privilege);
		return $this;
	}

	private function check($name)
	{
		if (isset($this->items[$name])) {
			throw new Mesour\InvalidStateException('Sub item with name ' . $this->items[$name] . ' is already exist.');
		}
	}

	public function reset($hard = false)
	{
		$this->privateSession->set('settings', []);
		foreach ($this->items as $item) {
			/** @var Extensions\SubItem\Items\Item $item */
			$item->reset();
		}
	}

	public function getOpened()
	{
		$output = [];
		$settings = $this->privateSession->get('settings', []);
		foreach ($settings as $name => $value) {
			if (!isset($this->items[$name])) {
				unset($settings[$name]);
				continue;
			}
			/** @var Extensions\SubItem\Items\Item $item */
			$item = $this->items[$name];

			$keys = $item->getKeys();
			if (count($keys) > 0 && count($keys) < count($settings[$name])) {
				while (count($keys) < count($settings[$name])) {
					array_shift($settings[$name]);
				}
			}
			foreach ($settings[$name] as $key => $i) {
				if (!isset($settings[$name][$key])) {
					unset($settings[$name][$key]);
					continue;
				}
				if (!$item->hasKey($settings[$name][$key])) {
					$item->addAlias($i, end($keys));
				}
				$output[$name]['keys'][] = $settings[$name][$key];
				if (!isset($output[$name]['item'])) {
					$output[$name]['item'] = $this->items[$name];
				}
			}
		}
		$this->privateSession->set('settings', $settings);
		return $output;
	}

	public function getItem($name)
	{
		if (!isset($this->items[$name])) {
			throw new Mesour\InvalidStateException('Item ' . $name . ' does not exist.');
		}
		return $this->items[$name];
	}

	public function getNames()
	{
		return array_keys($this->items);
	}

	public function getItemsCount()
	{
		return count($this->items);
	}

	public function getItems()
	{
		return $this->items;
	}

	public function hasSubItems()
	{
		return count($this->items) > 0;
	}

	public function handleToggleItem($key, $name)
	{
		$settings = $this->privateSession->get('settings', []);
		if (isset($settings[$name])) {
			if (in_array($key, $settings[$name])) {
				unset($settings[$name][array_search($key, $settings[$name])]);
			} else {
				$settings[$name][] = $key;
			}
		} else {
			$settings[$name][] = $key;
		}
		$this->privateSession->set('settings', $settings);
	}

	public function gridCreate($data = [])
	{
		$this->create();
	}

}
