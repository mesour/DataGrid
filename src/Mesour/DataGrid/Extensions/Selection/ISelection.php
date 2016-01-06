<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\Selection;

use Mesour;
use Mesour\Selection;


/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
interface ISelection extends Selection\ISelection, Mesour\DataGrid\Extensions\IHasColumn, Mesour\DataGrid\Extensions\IExtension
{

    /**
     * @return Links
     */
    public function getLinks();

}
