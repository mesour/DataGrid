<?php
/**
 * Mesour DataGrid
 *
 * @license LGPL-3.0 and BSD-3-Clause
 * @copyright (c) 2015 Matous Nemec <mesour.com>
 */

namespace Mesour\DataGrid;

use Mesour\ArrayManage\Searcher\Condition;
use Mesour\ArrayManage\Searcher\Select;

/**
 * @author mesour <mesour.com>
 * @package Mesour DataGrid
 */
class ArrayDataSource implements IDataSource
{

    private $primary_key = 'id';

    private $parent_key = 'parent_id';

    private $relations = array();

    private $related = array();

    /**
     * @var Select
     */
    protected $select;

    protected $data_arr = array();

    protected $structure = array();

    /**
     * @var Select
     */
    private $exportSelect;

    /**
     * @param array $data
     * @param array $relations
     * @throws Grid_Exception
     */
    public function __construct(array $data, array $relations = array())
    {
        if (!class_exists('\Mesour\ArrayManage\Searcher\Select')) {
            throw new Grid_Exception('Array data source required composer package "mesour/array-manager".');
        }
        $this->data_arr = $data;
        $this->relations = $relations;
    }

    /**
     * @return Select
     * @throws Grid_Exception
     */
    private function getExportSelect()
    {
        if(!$this->exportSelect) {
            $this->getSelect();
        }
        return $this->exportSelect;
    }

    public function fetchAllForExport()
    {
        return $this->getExportSelect()->fetchAll();
    }

    public function setPrimaryKey($primary_key)
    {
        $this->primary_key = $primary_key;
        return $this;
    }

    public function getPrimaryKey()
    {
        return $this->primary_key;
    }

    public function setParentKey($parent_key)
    {
        $this->parent_key = $parent_key;
        return $this;
    }

    public function getParentKey()
    {
        return $this->parent_key;
    }

    public function fetchAssoc()
    {
        $output = array();
        foreach ($this->fetchAll() as $value) {
            $output[$value[$this->parent_key]][] = $value;
        }
        return $output;
    }

    /**
     * Get array data count
     *
     * @return Integer
     */
    public function getTotalCount()
    {
        return $this->getSelect()->getTotalCount();
    }

    public function where($column, $value = NULL, $condition = NULL, $operator = 'and')
    {
        if (isset($this->structure[$column]) && $this->structure[$column] === 'date') {
            $value = $this->fixDate($value);
            $column = '__date_' . $column;
        }
        $this->getSelect()->where($column, $value, $condition, $operator);
        $this->getExportSelect()->where($column, $value, $condition, $operator);
        return $this;
    }

    static public function fixDate($date)
    {
        return is_numeric($date) ? $date : strtotime($date);
    }

    /**
     * Apply limit and offset
     *
     * @param Integer $limit
     * @param Integer $offset
     */
    public function applyLimit($limit, $offset = 0)
    {
        $this->getSelect()->limit($limit);
        $this->getSelect()->offset($offset);
    }

    /**
     * Get count after applied where
     *
     * @return Integer
     */
    public function count()
    {
        return $this->getSelect()->count();
    }

    /**
     * Get searched values witp applied limit, offset and where
     *
     * @return Array
     */
    public function fetchAll()
    {
        $out = $this->getSelect()->fetchAll();
        if (count($this->structure) > 0) {
            foreach ($out as $key => $val) {
                $this->removeStructureDate($out[$key]);
            }
        }
        return $out;
    }

    public function orderBy($row, $sorting = 'ASC')
    {
        $this->getSelect()->orderBy($row, $sorting);
    }

    private function customFilter($how)
    {
        switch ($how) {
            case 'equal_to';
                return Condition::EQUAL;
            case 'not_equal_to';
                return Condition::NOT_EQUAL;
            case 'bigger';
                return Condition::BIGGER;
            case 'not_bigger';
                return Condition::NOT_BIGGER;
            case 'smaller';
                return Condition::SMALLER;
            case 'not_smaller';
                return Condition::NOT_SMALLER;
            case 'start_with';
                return Condition::STARTS_WITH;
            case 'not_start_with';
                return Condition::NOT_STARTS_WITH;
            case 'end_with';
                return Condition::ENDS_WITH;
            case 'not_end_with';
                return Condition::NOT_ENDS_WITH;
            case 'equal';
                return Condition::CONTAINS;
            case 'not_equal';
                return Condition::NOT_CONTAINS;
            default:
                throw new Grid_Exception('Unexpected key for custom filtering.');
        }
    }

    public function applyCustom($column_name, array $custom, $type)
    {
        $values = array();
        if (!empty($custom['how1']) && !empty($custom['val1'])) {
            $values[] = $this->customFilter($custom['how1']);
        }
        if (!empty($custom['how2']) && !empty($custom['val2'])) {
            $values[] = $this->customFilter($custom['how2']);
        }
        if (count($values) === 2) {
            if ($custom['operator'] === 'and') {
                $operator = 'and';
            } else {
                $operator = 'or';
            }
        }
        foreach ($values as $key => $val) {
            $this->where($column_name, $custom['val' . ($key + 1)], $val, isset($operator) ? $operator : 'and');
        }
        return $this;
    }

    public function applyCheckers($column_name, array $value, $type)
    {
        foreach ($value as $val) {
            $this->where($column_name, $val, Condition::EQUAL, 'or');
        }
        return $this;
    }

    public function fetchFullData($date_format = 'Y-m-d')
    {
        $output = array();
        foreach ($this->data_arr as $data) {
            foreach ($data as $key => $val) {
                if ($val instanceof \DateTime) {
                    $data[$key] = $val->format($date_format);
                }
            }
            $output[] = $data;
        }
        return $output;
    }

    /**
     * Return first element from data
     *
     * @return Array
     */
    public function fetch()
    {
        $data = $this->getSelect()->fetch();
        if (!$data) {
            return array();
        }
        if (count($this->structure) > 0) {
            $this->removeStructureDate($data);
        }
        return $data;
    }

    /**
     * @param string $key
     * @param string $value
     * @return array
     * @throws Grid_Exception
     */
    public function fetchPairs($key, $value)
    {
        $data = $this->getSelect()->column($key)->column($value)
            ->fetchAll();

        $output = array();
        foreach ($data as $item) {
            $output[$item[$key]] = $item[$value];
        }
        return $output;
    }

    protected function removeStructureDate(&$out)
    {
        foreach ($this->structure as $name => $type) {
            switch ($type) {
                case 'date':
                    unset($out['__date_' . $name]);
                    break;
            }
        }
    }

    public function setStructure(array $structure)
    {
        $this->structure = $structure;
        return $this;
    }

    public function setRelated($table, $key, $column, $as = NULL, $primary = 'id')
    {
        $this->related[$table] = array($table, $key, $column, $as, $primary);
        $related = $this->related($table);

        foreach ($this->data_arr as $_key => $item) {
            $current = clone $related;
            if (isset($item[$key])) {
                $_item = $current->where($related->getPrimaryKey(), $item[$key], Condition::EQUAL)->fetch();
                $item_name = is_string($as) ? $as : $column;
                $this->data_arr[$_key][$item_name] = $_item[$column];
                $this->select = NULL;
            } else {
                throw new Grid_Exception('Column ' . $key . ' does not exist in data array.');
            }
            unset($current);
        }
        return $this;
    }

    /**
     * @param $table
     * @return $this
     * @throws Grid_Exception
     */
    public function related($table)
    {
        if (!$this->isRelated($table)) {
            throw new Grid_Exception('Relation ' . $table . ' does not exists.');
        }
        if (!isset($this->relations[$table]) || !$this->relations[$table] instanceof IDataSource) {
            $this->relations[$table] = new static($this->relations[$table]);
            $this->relations[$table]->setPrimaryKey($this->related[$table][4]);
        }
        return $this->relations[$table];
    }

    /**
     * @param $table
     * @return bool
     */
    public function isRelated($table)
    {
        return isset($this->related[$table]);
    }

    /**
     * @return array
     */
    public function getAllRelated()
    {
        return $this->related;
    }

    /**
     * @return Select
     * @throws Grid_Exception
     */
    protected function getSelect()
    {
        if (!$this->select) {
            if (count($this->structure)) {
                foreach ($this->structure as $name => $value) {
                    switch ($value) {
                        case 'date':
                            foreach ($this->data_arr as $key => $item) {
                                if (!array_key_exists($name, $item)) {
                                    throw new Grid_Exception('Column ' . $name . ' does not exists in source array.');
                                }
                                $this->data_arr[$key]['__date_' . $name] = $this->fixDate($item[$name]);
                            }
                            break;
                    }
                }
            }
            $this->select = new Select($this->data_arr);
        }
        if (!$this->exportSelect) {
            $this->exportSelect = clone $this->select;
        }
        return $this->select;
    }

}