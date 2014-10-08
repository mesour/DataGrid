<?php

namespace DataGrid\Render;

use \DataGrid\Column;

/**
 * Description of \DataGrid\Render\Row
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
abstract class Row{

	/**
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * @var array
	 */
	protected $cells = array();

	protected $rowData;

	/**
	 * @var Body
	 */
	protected $body;

	public function __construct($rowData) {
		$this->rowData = $rowData;
	}

	public function setAttributes(array $attributes = array()) {
		$this->attributes = $attributes;
	}

	public function addCell(Cell $cell) {
		$this->cells[] = $cell;
	}

	public function setBody(Body $body) {
		$this->body = $body;
	}

	abstract public function create();

}