<?php

namespace Mesour\DataGrid\Components;

use Mesour\DataGrid\Column,
    Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class DropDownHeader extends DropDownItem {

	const NAME = 'name';

	public function setName($name) {
		$this->option[self::NAME] = $name;
		return $this;
	}

	public function create($data) {
		if(isset($this->option[self::ATTRIBUTES]['class'])) {
			$this->option[self::ATTRIBUTES]['class'] = 'dropdown-header '  . $this->option[self::ATTRIBUTES]['class'];
		} else {
			$this->option[self::ATTRIBUTES]['class'] = 'dropdown-header';
		}
		if(!isset($this->option[self::ATTRIBUTES]['role'])) {
			$this->option[self::ATTRIBUTES]['role'] = 'presentation';
		}

		$header = Html::el('li', $this->option[self::ATTRIBUTES]);
		$header->setText($this->getTranslator() ? $this->getTranslator()->translate($this->option[self::NAME]) : $this->option[self::NAME]);

		return $header;
	}

}