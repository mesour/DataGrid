<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Renderer;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class GridRenderer implements IGridRenderer
{

	private $components = [];

	public function setComponent($type, $component)
	{
		if (!$component instanceof Mesour\Components\Utils\IString && !is_string($component) && !is_int($component)) {
			throw new Mesour\InvalidArgumentException('Component must be string or int or instanceof Mesour\Components\IString');
		}
		$this->components[$type] = $component;
	}

	public function getComponent($type)
	{
		if (!$this->components[$type]) {
			throw new Mesour\InvalidStateException('Component with type ' . $type . ' does not exists. Can use only ' . implode('|', array_keys($this->components)) . '.');
		}
		return isset($this->components[$type]) ? $this->components[$type] : null;
	}

	/**
	 * @return Mesour\Components\Utils\Html
	 */
	public function getWrapper()
	{
		return $this->getComponent('wrapper');
	}

	public function getSnippetId()
	{
		return $this->getComponent('snippet');
	}

	public function renderGrid()
	{
		echo $this->getComponent('grid');
	}

	public function renderPager()
	{
		echo $this->getComponent('pager');
	}

	public function renderFilter()
	{
		echo $this->getComponent('filter');
	}

	public function renderExport()
	{
		echo $this->getComponent('export');
	}

	public function renderSelection()
	{
		echo $this->getComponent('selection');
	}

	public function render()
	{
		echo $this->__toString();
	}

	public function __toString()
	{
		try {
			$wrapper = $this->getWrapper();
			$wrapper->id($this->getSnippetId());

			if (isset($this->components['filter'])) {
				$wrapper->insert(0, $this->getComponent('filter'));
			}

			$wrapper->insert(1, $this->getComponent('grid'));

			if (isset($this->components['pager'])) {
				$wrapper->insert(2, $this->getComponent('pager'));
			}
			if (isset($this->components['selection'])) {
				$wrapper->insert(3, $this->getComponent('selection'));
			}
			if (isset($this->components['export'])) {
				$wrapper->insert(4, $this->getComponent('export'));
			}
			$wrapper->insert(5, '<hr class="mesour-clear">');
			return (string) $wrapper;
		} catch (\Exception $e) {
			trigger_error($e->getMessage(), E_USER_WARNING);
			return '';
		}
	}

	public function __clone()
	{
		$this->components = [];
	}

}
