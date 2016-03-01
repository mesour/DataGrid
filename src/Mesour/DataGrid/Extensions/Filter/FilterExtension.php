<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\Filter;

use Mesour;


/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class FilterExtension extends Mesour\UI\Filter implements IFilter
{

	private $isInline = true;

	private $disabled = false;

	/** @var Mesour\Components\Utils\Html|string */
	private $createdFilter;

	protected $filterIcon = 'filter';

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

	public function setInline($inline = true)
	{
		$this->isInline = $inline;
		$this->setupInlineFilterAttribute();
		return $this;
	}

	public function setFilterIcon($filterIcon)
	{
		$this->filterIcon = $filterIcon;
		return $this;
	}

	public function isInline()
	{
		return $this->isInline;
	}

	public function gridCreate($data = [])
	{
		$this->onFilter[] = function (IFilter $_filter) {
			$this->updateFilter($_filter);
			$this->getGrid()->onFilter($_filter);
		};
		$this->setSource($this->getGrid()->getSource());
		$fullData = $this->beforeCreate();

		foreach ($this->getGrid()->getColumns() as $column) {
			if ($column instanceof Mesour\DataGrid\Column\IFiltering) {
				if ($column->hasFiltering()) {
					$column->attachToFilter($this, count($fullData) > 0);
				}
			}
		}
		if (!$this->isInline()) {
			$this->setOption('data', $data);
			$this->createdFilter = $this->create();
		} else {
			$this->beforeRender();
			$this->createdFilter = $this->createHiddenInput($fullData);
		}
		$this->updateFilter($this);
	}

	/**
	 * @return Mesour\DataGrid\ExtendedGrid
	 */
	public function getGrid()
	{
		return $this->getParent();
	}

	public function createInstance(Mesour\DataGrid\Extensions\IExtension $extension, $name = null)
	{
		$this->setupInlineFilterAttribute();
	}

	private function setupInlineFilterAttribute()
	{
		$this->getGrid()->setAttribute(
			'data-mesour-enabled-filter', (int)($this->isInline() && !$this->isDisabled())
		);
	}

	public function afterGetCount($count)
	{
		foreach ($this->getGrid()->getColumns() as $column) {
			if (
				$column instanceof Mesour\DataGrid\Column\IFiltering
				&& $this->isInline()
				&& isset($this[$column->getName()])
			) {
				$column->setInline();
				$this[$column->getName()]->setText($this->createNewIcon($this->filterIcon));
			}
		}
	}

	public function beforeFetchData($data = [])
	{
	}

	public function afterFetchData($currentData, $data = [], $rawData = [])
	{
		$referenceSettings = $this->getSource()->getReferenceSettings();
		foreach ($this->getGrid()->getColumns() as $column) {
			if ($column instanceof Mesour\DataGrid\Column\IFiltering && isset($referenceSettings[$column->getName()])) {
				$this[$column->getName()]->setReferenceSettings($referenceSettings[$column->getName()]);
			} else {
				continue;
			}
		}
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
	 * @param IFilter $filter
	 * @throws
	 * @internal
	 */
	private function updateFilter(IFilter $filter)
	{
		if (!$this->isDisabled()) {
			$source = $this->getGrid()->getSource();
			foreach ($filter->getValues() as $name => $value) {
				$type = isset($value['type']) ? $value['type'] : 'text';
				foreach ($value as $key => $val) {
					switch ($key) {
						case 'checkers':
							$source->applyCheckers($name, $val, $type);
							break;
						case 'custom':
							$source->applyCustom($name, $val, $type);
							break;
					}
				}
			}
		}
	}

}