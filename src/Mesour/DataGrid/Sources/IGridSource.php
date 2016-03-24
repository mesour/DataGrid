<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Sources;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
interface IGridSource extends Mesour\Filter\Sources\IFilterSource
{

	public function fetchForExport();

	public function getColumnNames();

}
