<?php

namespace Mesour\DataGrid;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @author  Martin Procházka <juniwalk@outlook.cz>
 * @package Mesour DataGrid
 */
class DoctrineDataSource implements IDataSource
{
    /**
     * Name of primary column name.
     * @return string
     */
    protected $primary_key = 'id';

    /**
     * Name of parent column name.
     * @return string
     */
    protected $parent_key = 'parent_id';

    /**
     * QueryBuilder instance.
     * @var QueryBuilder
     */
    protected $qb;


    /**
     * Initialize Doctrine data source with QueryBuilder instance.
     * @param QueryBuilder  $qb  Source of data
     */
    public function __construct(QueryBuilder $qb)
    {
        $this->qb = clone $qb;
    }


    /**
     * Get total count without applied where and limit.
     * @return int
     */
    public function getTotalCount()
    {
        return 0;
    }


    /**
     * Get every single row of the table.
     * @return array
     */
    public function fetchFullData()
    {
        $qb = clone $this->qb;
        return $qb->where([])->setMaxResults(null)->setFirstResult(null)->getQuery()->getResult(Query::HYDRATE_ARRAY);
        return [];
    }


    /**
     * Apply limit and offset.
     * @param  int  $limit   Number of rows
     * @param  int  $offset  Rows to skip
     * @return static
     */
    public function applyLimit($limit, $offset = 0)
    {
        $this->qb->setMaxResults($limit)->setFirstResult($offset);
        return $this;
    }


    /**
     * Apply custom WHERE criteria.
     * @param  string  $column  Column name
     * @param  array   $custom  Custom criteria
     * @param  string  $type    Column type
     * @return static
     */
    public function applyCustom($column, array $custom, $type)
    {
        return $this;
    }


    /**
     * @param  string  $column  Column name
     * @param  array   $custom  Custom criteria
     * @param  string  $type    Column type
     * @return static
     */
    public function applyCheckers($column, array $value, $type)
    {
        return $this;
    }


    /**
     * Add where condition.
     * @param  mixed  $args
     * @return static
     */
    public function where($args)
    {
        return $this;
    }


    /**
     * Add ORDER BY directive to the criteria.
     * @param  string  $column   Column name
     * @param  string  $sorting  Sorting direction
     * @return static
     */
    public function orderBy($column, $sorting = 'ASC')
    {
        $this->qb->autoJoinOrderBy($column, $sorting);
        return $this;
    }


    /**
     * Get count with applied where without limit.
     * @return int
     */
    public function count()
    {
        return (new Paginator($this->qb))->count();
    }


    /**
     * Get first element from data.
     * @return array
     */
    public function fetch()
    {
        $qb = clone $this->qb;
        return $qb->setMaxResults(1)->getQuery()->getSingleResult(Query::HYDRATE_ARRAY);
    }


    /**
     * Get data with applied where, limit and offset.
     * @return array
     */
    public function fetchAll()
    {
        return $this->qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }


    /**
     * Get data with applied where without limit and offset.
     * @return array
     */
    public function fetchAllForExport()
    {
        return [];
    }


    /**
     * Get data with applied where, limit and offset and returns tree.
     * @return array
     */
    public function fetchAssoc()
    {
		return [];
    }


    /**
     * Get primary column name.
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primary_key;
    }


    /**
     * Set new primary column name.
     * @param  string  $column  Column name
     * @return static
     */
    public function setPrimaryKey($column)
    {
        $this->primary_key = $column;
        return $this;
    }


    /**
     * Parent column name getter.
     * @return string
     */
    public function getParentKey()
    {
        return $this->parent_key;
    }


    /**
     * Set new parent column name.
     * @param  string  $column  Column name
     * @return static
     */
    public function setParentKey($parent_key)
    {
        $this->parent_key = $parent_key;
        return $this;
    }
}
