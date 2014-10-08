<?php

namespace DataGrid\Render;

use \DataGrid\Column;

/**
 * Description of \DataGrid\Render\Cell
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
abstract class Cell{

	protected $rowData;

	/**
	 * @var \DataGrid\Column\IColumn
	 */
	protected $column;

	public function __construct($rowData, Column\IColumn $column) {
		$this->rowData = $rowData;
		$this->column = $column;
	}

	abstract public function create();

}