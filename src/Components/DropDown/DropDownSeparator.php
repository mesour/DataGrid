<?php

namespace Mesour\DataGrid\Components;

use Mesour\DataGrid\Column,
    Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class DropDownSeparator extends DropDownItem {

	const NAME = 'name';

	public function setName($name) {
		$this->option[self::NAME] = $name;
		return $this;
	}

	public function create($data) {
		if(isset($this->option[self::ATTRIBUTES]['class'])) {
			$this->option[self::ATTRIBUTES]['class'] = 'divider '  . $this->option[self::ATTRIBUTES]['class'];
		} else {
			$this->option[self::ATTRIBUTES]['class'] = 'divider';
		}
		if(!isset($this->option[self::ATTRIBUTES]['role'])) {
			$this->option[self::ATTRIBUTES]['role'] = 'presentation';
		}

		$separator = Html::el('li', $this->option[self::ATTRIBUTES]);

		return $separator;
	}

}