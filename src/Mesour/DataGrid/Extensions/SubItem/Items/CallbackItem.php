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
class CallbackItem extends Item
{

	public function __construct(Mesour\DataGrid\Extensions\SubItem\ISubItem $parent, $name, $description = null)
	{
		parent::__construct($parent, $name, $description);
	}

	public function render($key = null, $rowData = null, $rawData = null)
	{
		if (is_null($key) || is_null($rowData)) {
			return '';
		}
		return parent::invoke([$rawData], null, null);
	}

	public function reset()
	{

	}

	public function invoke(array $args = [], $name, $key)
	{

	}

}