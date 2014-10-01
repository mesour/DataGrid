<?php

namespace DataGrid\Column;

/**
 * Description of \DataGrid\Column\IColumn
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
interface IColumn {

	public function createHeader();

	public function createBody($data);

} 