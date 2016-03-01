<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Column;

use Mesour;
use Mesour\DataGrid\Column;


/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
abstract class Filtering extends Ordering implements IFiltering
{

	private $filtering = true;

	private $inline = false;

	/** @var Mesour\DataGrid\Extensions\Filter\IFilter */
	private $filter;

	protected $filterItemSizeClass = 'btn-xs';

	/**
	 * @param bool $filtering
	 * @return $this
	 */
	public function setFiltering($filtering = true)
	{
		$this->filtering = (bool)$filtering;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function hasFiltering()
	{
		return $this->filtering;
	}

	/**
	 * @param bool $inline
	 * @return $this
	 */
	public function setInline($inline = true)
	{
		$this->inline = (bool)$inline;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isInline()
	{
		return $this->inline;
	}

	protected function setFilter(Mesour\DataGrid\Extensions\Filter\IFilter $filter)
	{
		$this->filter = $filter;
	}

	public function attachToFilter(Mesour\DataGrid\Extensions\Filter\IFilter $filter, $hasCheckers)
	{
		$this->setFilter($filter);
	}

	public function validate(array $rowData, $data = [])
	{
		parent::validate($rowData, $data);

		if ($this->hasFiltering()) {
			foreach ($rowData as $item) {
				if (!isset($item[$this->getName()])) {
					throw new Mesour\InvalidStateException(
						sprintf('If use filtering, column key "%s" must exists in data.', $this->getName())
					);
				}
				break;
			}
		}
	}

	public function getHeaderAttributes()
	{
		if (isset($this->filter[$this->getName()]) && $this->inline && $this->filtering) {
			return array_merge([
				'data-with-filter' => '1',
			], parent::getHeaderAttributes());
		}
		return parent::getHeaderAttributes();
	}

	public function getHeaderContent()
	{
		$parentContent = parent::getHeaderContent();
		if ($this->inline) {
			$filterItem = $this->filter->getItem($this->getName());
			$filterItem->getButtonPrototype()
				->class($this->filterItemSizeClass, true);
			$parentContent .= ' ' . $filterItem->render();

			return $parentContent;
		} else {
			return $parentContent;
		}
	}

}
