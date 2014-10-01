<?php

namespace DataGrid;

/**
 * Description of \DataGrid\IColumn
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
interface IColumn {

	public function createHeader();

	public function createBody($data);

} 