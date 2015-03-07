<?php

namespace Mesour\DataGrid\Render;

use Mesour\DataGrid\Column;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
abstract class Attributes {

	/**
	 * @var array
	 */
	protected $attributes = array();

	public function setAttributes(array $attributes = array()) {
		$this->attributes = $attributes;
	}

	/**
	 * @param $key
	 * @param $value
	 * @param bool $append
	 * @deprecated
	 */
	public function addAttribute($key, $value, $append = FALSE) {
		$this->setAttribute($key, $value, $append);
	}

	public function setAttribute($key, $value, $append = FALSE) {
		if($append && isset($this->attributes[$key])) {
			$this->attributes[$key] = $this->attributes[$key] . ' ' . $value;
		} else {
			$this->attributes[$key] = $value;
		}
		return $this;
	}

}