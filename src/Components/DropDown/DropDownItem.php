<?php

namespace Mesour\DataGrid\Components;

use Mesour\DataGrid\Column,
    Mesour\DataGrid\Setting;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
abstract class DropDownItem extends Setting {

	/**
	 * Possible option key
	 */
	const ATTRIBUTES = 'attributes';

	public function setAttributes(array $attributes) {
		$this->option[self::ATTRIBUTES] = $attributes;
		return $this;
	}

	public function setAttribute($key, $value, $append = FALSE) {
		if($append && isset($this->option[self::ATTRIBUTES][$key])) {
			$this->option[self::ATTRIBUTES][$key] = $this->option[self::ATTRIBUTES][$key] . ' ' . $value;
		} else {
			$this->option[self::ATTRIBUTES][$key] = $value;
		}
		return $this;
	}

	protected function setDefaults() {
		return array(
		    self::ATTRIBUTES => array()
		);
	}

	abstract public function create($data);

}