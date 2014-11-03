<?php

namespace Mesour\DataGrid\Render;

use Mesour\DataGrid\Column;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
interface IRendererFactory{

	public function createCell($rowData, Column\IColumn $column);

	public function createRow($rowData);

	public function createHeader();

	public function createBody();

	public function createTable();

}