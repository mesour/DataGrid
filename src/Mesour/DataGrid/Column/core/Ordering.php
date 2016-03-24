<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Column;

use Mesour;
use Mesour\DataGrid\Column;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
abstract class Ordering extends BaseColumn implements IOrdering
{

	private $ordering = true;

	public function setOrdering($ordering = true)
	{
		$this->ordering = (bool) $ordering;
		return $this;
	}

	public function hasOrdering()
	{
		return $this->ordering;
	}

	public function validate(array $rowData, $data = [])
	{
		parent::validate($rowData, $data);

		if ($this->hasOrdering() && count($rowData) > 0) {
			$item = reset($rowData);
			if (!array_key_exists($this->getName(), $item)) {
				throw new Mesour\InvalidStateException(
					sprintf('If use ordering, column key "%s" must exists in data.', $this->getName())
				);
			}
		}
	}

	public function getHeaderContent()
	{
		if ($this->ordering) {
			/** @var \Mesour\DataGrid\Extensions\Ordering\OrderingExtension $component */
			$component = $this->getGrid('ordering');
			$ordering = $component->getOrdering($this->getName());

			$link = Mesour\Components\Utils\Html::el('a', [
				'href' => $this->getGrid('ordering')->createLink('ordering', ['key' => $this->getName()]),
				'class' => 'ordering' . (!is_null($ordering) ? (' ' . strtolower($ordering)) : ''),
				'data-mesour' => 'ajax',
			]);

			$icon = $this->createNewIcon('cog');
			$link->setText(parent::getHeaderContent());
			if ($this instanceof Column\Number || $this instanceof Column\Date) {
				$iconName = 'sort-numeric';
			} elseif ($this instanceof Column\Status) {
				$iconName = 'sort-amount';
			} else {
				$iconName = 'sort-alpha';
			}

			foreach (['-asc no-sort', '-asc order-asc', '-desc order-desc'] as $suffix) {
				$icon->setType($iconName . $suffix);
				$link->add($icon->render());
			}

			return $link . $this->getFilterResetButton();
		} else {
			return parent::getHeaderContent() . $this->getFilterResetButton();
		}
	}

}
