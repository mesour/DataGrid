<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
interface IHasColumn
{

	/**
	 * @return Mesour\DataGrid\Column\IColumn
	 */
	public function getSpecialColumn();

	/**
	 * @return string
	 */
	public function getSpecialColumnName();

	/**
	 * @return bool
	 */
	public function isGetSpecialColumnUsed();

}
