<?php

namespace Mesour\DataGrid\Render;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
abstract class Header {

	/**
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * @var array
	 */
	protected $cells = array();

	public function setAttributes(array $attributes = array()) {
		$this->attributes = $attributes;
	}

	public function addCell(HeaderCell $cell) {
		$this->cells[] = $cell;
	}

	abstract public function create();

}