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
class TemplateItem extends Item
{

	private $templatePath;

	private $block = null;

	private $template;

	public function __construct(
		Mesour\DataGrid\Extensions\SubItem\ISubItem $parent,
		$name,
		$description = null,
		Mesour\DataGrid\TemplateFile $template = null,
		$templatePath = null,
		$block = null
	)
	{
		parent::__construct($parent, $name, $description);
		$this->template = $template;
		$this->templatePath = $templatePath;
		$this->block = $block;
	}

	public function render()
	{
		$this->template->_template_path = $this->templatePath;
		$this->template->_block = false;
		if (!is_null($this->block) && is_string($this->block)) {
			$this->template->_block = $this->block;
		}
		return $this->template;
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
