<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Column;

use Mesour;
use Mesour\Table\Render;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
interface IColumn extends Render\IColumn, Mesour\Components\Control\IControl
{

	/**
	 * @param Mesour\DataGrid\Extensions\Filter\IFilter $filter
	 * @internal
	 */
	public function setFilterReset(Mesour\DataGrid\Extensions\Filter\IFilter $filter);

	public function setDisabled($disabled = true);

	public function isDisabled();

	public function validate(array $rowData, $data = []);

}
