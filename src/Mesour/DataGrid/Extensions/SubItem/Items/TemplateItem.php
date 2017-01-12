<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\SubItem\Items;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class TemplateItem extends Item
{

	use Mesour\Template\TemplateTrait;

	public function __construct(Mesour\DataGrid\Extensions\SubItem\ISubItem $parent, $name, $description = null)
	{
		parent::__construct($parent, $name, $description);
	}

	public function render()
	{
		return $this->getTemplateFile();
	}

	public function reset()
	{

	}

	public function invoke(array $args = [], $name, $key)
	{
		$arguments = [$this->render()];
		$arguments = array_merge($arguments, $args);
		return parent::invoke($arguments, $name, $key);
	}

}
