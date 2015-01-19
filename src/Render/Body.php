<?php

namespace Mesour\DataGrid\Render;

use Mesour\DataGrid\Column;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
abstract class Body {

	/**
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * @var array
	 */
	protected $rows = array();

	public function setAttributes(array $attributes = array()) {
		$this->attributes = $attributes;
	}

	public function addRow(Row $row) {
		$this->rows[] = $row;
	}

	abstract public function create();

}