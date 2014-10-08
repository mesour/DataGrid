<?php

namespace DataGrid\Render;

/**
 * Description of \DataGrid\Render\Header
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
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