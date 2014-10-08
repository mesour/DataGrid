<?php

namespace DataGrid\Render;

use \DataGrid\Column;
use DataGrid\Render\Tree\Renderer;

/**
 * Description of \DataGrid\Render\IRendererFactory
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
interface IRendererFactory{

	public function createCell($rowData, Column\IColumn $column);

	public function createRow($rowData);

	public function createHeader();

	public function createBody();

	public function createTable();

}