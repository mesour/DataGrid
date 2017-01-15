<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\SimpleFilter;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class SimpleFilterExtension extends Mesour\UI\SimpleFilter implements ISimpleFilter
{

	use Mesour\Components\Security\Authorised;

	private $disabled = false;

	/** @var Mesour\Components\Utils\Html|string */
	private $createdFilter;

	/**
	 * @return bool
	 */
	public function isDisabled()
	{
		return $this->disabled;
	}

	public function setDisabled($disabled = true)
	{
		$this->disabled = (bool) $disabled;
		return $this;
	}

	public function gridCreate($data = [])
	{
		$this->onFilter[] = function (ISimpleFilter $currentFilter) {
			$this->updateFilter($currentFilter);
			$this->getGrid()->onFilter($currentFilter);
		};
		$this->setSource($this->getGrid()->getSource());

		$this->setOption('data', $data);
		$this->createdFilter = $this->create();
		$this->updateFilter($this);
	}

	/**
	 * @return Mesour\DataGrid\ExtendedGrid|Mesour\Components\Control\IControl
	 */
	public function getGrid()
	{
		return $this->getParent();
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

	public function afterFetchData($currentData, $data = [], $rawData = [])
	{

	}

	public function attachToRenderer(Mesour\DataGrid\Renderer\IGridRenderer $renderer, $data = [], $rawData = [])
	{
		$filterPrototype = $this->getGrid()->getFilterPrototype();
		$filterPrototype->add($this->createdFilter);
		$renderer->setComponent('filter', $filterPrototype);
	}

	public function reset($hard = false)
	{

	}

	/**
	 * @param ISimpleFilter $filter
	 * @throws
	 */
	private function updateFilter(ISimpleFilter $filter)
	{
		if (!$this->isDisabled()) {
			$source = $this->getGrid()->getSource();
			$source->applySimple($filter->getQuery(), $filter->getAllowedColumns());
		}
	}

}
