<?php

namespace Mesour\DataGrid\Extensions;

use Mesour\DataGrid\Column,
    Mesour\DataGrid\Setting;
use Nette\Object;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class SelectionHelper extends Setting {

	private $name;

	private $value;

	public function __construct($name, $value) {
		$this->name = $name;
		$this->value = $value;
	}

	public function getName() {
		return $this->getTranslator() ? $this->getTranslator()->translate($this->name) : $this->name;
	}

	public function getValue() {
		return $this->value;
	}

}