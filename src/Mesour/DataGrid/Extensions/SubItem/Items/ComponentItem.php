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
class ComponentItem extends Item
{

	public function __construct(Mesour\DataGrid\Extensions\SubItem\ISubItem $parent, $name, $description = null, $component = null)
	{
		parent::__construct($parent, $name, $description);
		$i = 0;
		while ($i < (is_null($this->pageLimit) ? self::DEFAULT_COUNT : $this->pageLimit)) {
			if (!$component instanceof Mesour\Components\Control\IControl) {
				Mesour\Components\Utils\Helpers::invokeArgs($component, [$this->getParent()->getParent(), $name . $i]);
			} else {
				$this->getGrid()->addComponent($component, $name . $i);
			}
			$this->keys[] = $i;
			$i++;
		}
	}

	public function render($key = null)
	{
		if (is_null($key)) {
			return '';
		}
		/** @var Mesour\Components\Control\IControl $component */
		$component = $this->getGrid()->getComponent($this->name . $this->getTranslatedKey($key));
		return $component->create();
	}

	public function invoke(array $args = [], $name, $key)
	{
		$arguments = [$this->getGrid()->getComponent($name . $key)];
		$arguments = array_merge($arguments, $args);
		return parent::invoke($arguments, $name, $key);
	}

	public function reset()
	{

	}

}
