<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\Filter;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
interface IFilter extends Mesour\Filter\IFilter, Mesour\DataGrid\Extensions\IExtension
{

	public function setInline($inline = true);

	public function isInline();

	public function beforeCreate();

}
