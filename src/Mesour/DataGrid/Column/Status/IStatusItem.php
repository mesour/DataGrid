<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Column\Status;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
interface IStatusItem extends Mesour\Components\Control\IAttributesControl
{

	public function isActive($columnName, $data);

	public function setStatus($status, $statusName, $selectionTitle = null);

	/**
	 * @return array|null   [$this->status => $this->statusName]
	 */
	public function getStatusOptions();

	public function getStatus();

	public function setPermission($resource = null, $privilege = null);

	public function getStatusName();

}
