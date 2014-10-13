<?php

namespace DataGrid\Render\Tree;

use \DataGrid\Column,
    DataGrid\Render;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class RendererFactory implements Render\IRendererFactory{

	public function createHeaderCell(Column\IColumn $column) {
		return new HeaderCell($column);
	}

	public function createCell($rowData, Column\IColumn $column) {
		return new Cell($rowData, $column);
	}

	public function createRow($rowData) {
		return new Row($rowData);
	}

	public function createBody() {
		return new Body();
	}

	public function createRowGroup($group_id) {
		return new RowGroup($group_id);
	}

	public function createHeader() {
		return new Header();
	}

	public function createTable() {
		return new Renderer();
	}

}