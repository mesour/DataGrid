<?php

namespace Mesour\DataGrid\Render;

use Mesour\DataGrid\Column;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
interface IRendererFactory {

	/**
	 * @param Column\IColumn $column
	 * @return HeaderCell
	 */
	public function createHeaderCell(Column\IColumn $column);

	/**
	 * @param Column\IColumn $column
	 * @return Cell
	 */
	public function createCell($rowData, Column\IColumn $column);

	/**
	 * @return Row
	 */
	public function createRow($rowData);

	/**
	 * @return Header
	 */
	public function createHeader();

	/**
	 * @return Body
	 */
	public function createBody();

	/**
	 * @return Renderer
	 */
	public function createTable();

}