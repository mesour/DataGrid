<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\SubItem\Items;

use Mesour;


/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class TemplateItem extends Item
{

    private $template_path;

    private $block = NULL;

    private $template;

    public function __construct(
        Mesour\DataGrid\Extensions\SubItem\ISubItem $parent, $name,
        $description = NULL, Mesour\DataGrid\TemplateFile $template = NULL,
        $template_path = NULL, $block = NULL
    )
    {
        parent::__construct($parent, $name, $description);
        $this->template = $template;
        $this->template_path = $template_path;
        $this->block = $block;
    }

    public function render()
    {
        $this->template->_template_path = $this->template_path;
        $this->template->_block = FALSE;
        if (!is_null($this->block) && is_string($this->block)) {
            $this->template->_block = $this->block;
        }
        return $this->template;
    }

    public function reset()
    {

    }

    public function invoke(array $args = [], $name, $key)
    {
        $arguments = [$this->render()];
        $arguments = array_merge($arguments, $args);
        return parent::invoke($arguments, $name, $key);
    }

}