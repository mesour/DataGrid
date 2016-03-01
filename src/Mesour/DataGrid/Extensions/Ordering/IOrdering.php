<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\Ordering;

use Mesour;


/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
interface IOrdering extends Mesour\DataGrid\Extensions\IExtension
{

	public function setDefaultOrder($key, $sorting = 'ASC');

	public function enableMulti();

}
