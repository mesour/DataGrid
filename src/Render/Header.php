<?php

namespace Mesour\DataGrid\Render;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
abstract class Header extends Attributes {

	protected $header_attributes = array();

	/**
	 * @var array
	 */
	protected $cells = array();

	public function addCell(HeaderCell $cell) {
		$this->cells[] = $cell;
	}

	public function setTHeadAttributes(array $header_attributes) {
		$this->header_attributes = $header_attributes;
	}

	abstract public function create();

}