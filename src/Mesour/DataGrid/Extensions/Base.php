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
abstract class Base extends Mesour\UI\Control implements IExtension
{

    private $disabled = FALSE;

    /**
     * @return Mesour\DataGrid\ExtendedGrid
     */
    public function getGrid()
    {
        return $this->getParent();
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    public function setDisabled($disabled = TRUE)
    {
        $this->disabled = (bool)$disabled;
        return $this;
    }

    public function gridCreate($data = [])
    {
    }

    public function createInstance(IExtension $extension, $name = NULL)
    {
    }

    public function afterGetCount($count)
    {
    }

    public function beforeFetchData($data = [])
    {
    }

    public function afterFetchData($currentData, $data = [], $rawData = [])
    {
    }

    public function attachToRenderer(Mesour\DataGrid\Renderer\IGridRenderer $renderer, $data = [], $rawData = [])
    {
    }

    public function reset($hard = FALSE)
    {
    }

}