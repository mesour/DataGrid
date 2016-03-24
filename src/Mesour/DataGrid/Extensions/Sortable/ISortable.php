<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\Sortable;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
interface ISortable extends Mesour\DataGrid\Extensions\IExtension, Mesour\DataGrid\Extensions\IHasColumn
{

	/**
	 * @param string $columnName
	 * @return mixed
	 */
	public function setColumnName($columnName);

	public function setPermission($resource, $privilege);

	public function isGetSpecialColumnUsed();

	public function handleSortData($data, $item);

}
