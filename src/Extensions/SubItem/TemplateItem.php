<?php

namespace Mesour\DataGrid\Extensions;

use Mesour\DataGrid\BasicGrid;
use Nette\Application\UI\ITemplate;
use Nette\ComponentModel\IComponent;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class TemplateItem extends Item {

	private $template_path;

	private $block = NULL;

	private $template;

	public function __construct(IComponent $parent, $name, $description = NULL, ITemplate $template = NULL, $template_path = NULL, $block = NULL) {
		parent::__construct($parent, $name, $description);
		$this->template = $template;
		$this->template_path = $template_path;
		$this->block = $block;
	}

	public function render() {
		$this->template->_template_path = $this->template_path;
		if(!is_null($this->block) && is_string($this->block)) {
			$this->template->_block = $this->block;
		} else {
			$this->template->_block = FALSE;
		}
		return $this->template;
	}

	public function reset() {

	}

	public function invoke(array $args = array(), $name, $key) {
		$arguments = array($this->render());
		$arguments = array_merge($arguments, $args);
		return parent::invoke($arguments, $name, $key);
	}

}