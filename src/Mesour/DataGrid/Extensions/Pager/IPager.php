<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\Pager;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
interface IPager extends Mesour\Pager\IPager, Mesour\DataGrid\Extensions\IExtension
{

	public function beforeRender();

	public function reset($hard = false);

	public function handleSetPage($page = null);

}
