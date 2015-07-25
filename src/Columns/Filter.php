<?php

namespace Mesour\DataGrid\Column;

use Nette\ComponentModel\IComponent;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
abstract class Filter extends BaseOrdering
{

    /**
     * Possible option key
     */
    const FILTERING = 'filtering';

    private $realColumnName;

    public function setGridComponent(IComponent $grid)
    {
        parent::setGridComponent($grid);
        if ($this->realColumnName) {
            $this->getGrid()->addRealColumnName($this->option[self::ID], $this->realColumnName);
        }
        return $this;
    }

    public function setFiltering($filtering)
    {
        $this->option[self::FILTERING] = (bool)$filtering;
        return $this;
    }

    public function setRealColumnName($realColumnName)
    {
        $this->realColumnName = $realColumnName;
        return $this;
    }

    protected function setDefaults()
    {
        return array_merge(parent::setDefaults(), array(
            self::FILTERING => TRUE
        ));
    }

    abstract function getTemplateFile();

}