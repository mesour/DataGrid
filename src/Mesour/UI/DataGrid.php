<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\UI;

use Mesour;
use Mesour\DataGrid\Column;


/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class DataGrid extends Mesour\DataGrid\SubItemGrid
{

    /**
     * @param $name
     * @param string|null $header
     * @return Column\Text
     */
    public function addText($name, $header = NULL)
    {
        $column = new Column\Text;
        $this->setColumn($column, $name, $header);
        return $column;
    }

    /**
     * @param $name
     * @param string|null $header
     * @return Column\Number
     */
    public function addNumber($name, $header = NULL)
    {
        $column = new Column\Number;
        $this->setColumn($column, $name, $header);
        return $column;
    }

    /**
     * @param $name
     * @param string|null $header
     * @return Column\Date
     */
    public function addDate($name, $header = NULL)
    {
        $column = new Column\Date;
        $this->setColumn($column, $name, $header);
        return $column;
    }

    /**
     * @param $name
     * @param string|null $header
     * @return Column\Container
     */
    public function addContainer($name, $header = NULL)
    {
        $column = new Column\Container;
        $this->setColumn($column, $name, $header);
        $column->setFiltering(FALSE)
            ->setOrdering(FALSE);
        return $column;
    }

    /**
     * @param $name
     * @param string|null $header
     * @return Column\Image
     */
    public function addImage($name, $header = NULL)
    {
        $column = new Column\Image;
        $this->setColumn($column, $name, $header);
        return $column;
    }

    /**
     * @param $name
     * @param string|null $header
     * @return Column\Status
     */
    public function addStatus($name, $header = NULL)
    {
        $column = new Column\Status;
        $this->setColumn($column, $name, $header);
        return $column;
    }

    /**
     * @param $name
     * @param string|null $header
     * @return Column\Template
     */
    public function addTemplate($name, $header = NULL)
    {
        $column = new Column\Template;
        $this->setColumn($column, $name, $header);
        return $column;
    }

    /**
     * @param $name
     * @param null $header
     * @return Column\Text
     */
    public function addColumn($name, $header = NULL)
    {
        return $this->addText($name, $header);
    }

}
