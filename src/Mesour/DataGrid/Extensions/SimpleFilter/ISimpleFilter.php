<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\SimpleFilter;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
interface ISimpleFilter extends Mesour\Filter\ISimpleFilter, Mesour\DataGrid\Extensions\IExtension
{

	public function beforeCreate();

}
