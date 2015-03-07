<?php

namespace Mesour\DataGrid\Render;

use Mesour\DataGrid\Column;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
abstract class Body extends Attributes {

	/**
	 * @var array
	 */
	protected $rows = array();

	public function addRow(Row $row) {
		$this->rows[] = $row;
	}

	abstract public function create();

}