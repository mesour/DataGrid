<?php

namespace Mesour\DataGrid\Column;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
abstract class InlineEdit extends Filter
{

    private $related;

    /**
     * Possible option key
     */
    const EDITABLE = 'editable';

    public function getBodyAttributes($data)
    {
        $attributes = parent::getBodyAttributes($data);
        if (isset($this->grid['editable']) && $this->isEditable()) {
            if ($this->related) {
                $attributes = array_merge($attributes, array(
                    'data-editable-related' => $this->related,
                ));
            }
        }
        return parent::mergeAttributes($data, $attributes);
    }

    protected function setDefaults()
    {
        return array_merge(parent::setDefaults(), array(
            self::EDITABLE => TRUE
        ));
    }

    public function setEditable($editable)
    {
        $this->option[self::EDITABLE] = (bool)$editable;
        return $this;
    }

    public function setRelated($table)
    {
        $this->related = (string)$table;
        return $this;
    }

    public function getRelated()
    {
        return $this->related;
    }

}