<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\Editable;

use Mesour;


/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
interface IEditable extends  Mesour\DataGrid\Extensions\IExtension
{

    public function handleEditCell(array $data);

    public function setPermission($resource, $privilege);

}
