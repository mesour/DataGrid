<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\SubItem;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
interface ISubItem extends Mesour\DataGrid\Extensions\IExtension
{

	public function getPageLimit();

	public function getItems();

	public function getOpened();

	public function hasSubItems();

	public function getItem($name);

	public function setPermission($resource, $privilege);

}
