<?php

namespace Mesour\DataGrid;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @author  Martin Procházka <juniwalk@outlook.cz>
 * @package Mesour DataGrid
 */
class DoctrineDataSource implements IDataSource
{
    /**
     * Doctrine QueryBuilder instance.
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * Mapping of columns to QueryBuilder
     * @var array
     */
    protected $columnMapping;

    /**
     * Count of all items.
     * @var int
     */
    protected $itemsTotalCount = 0;

    /**
     * Count of filtered items.
     * @var int
     */
    protected $itemsCount = 0;

    /**
     * Name of primary column name.
     * @var string
     */
    protected $primary_key = 'id';

    /**
     * Name of parent column name.
     * @var string
     */
    protected $parent_key = 'parent_id';


    /**
     * Initialize Doctrine data source with QueryBuilder instance.
     * @param QueryBuilder  $queryBuilder   Source of data
     * @param array         $columnMapping  Column name mapper
     */
    public function __construct(QueryBuilder $queryBuilder, array $columnMapping = array())
    {
        // Save copy of provided QueryBuilder
        $this->queryBuilder = clone $queryBuilder;
        $this->columnMapping = $columnMapping;
    }


    /**
     * Get instance of the QueryBuilder.
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }


    /**
     * Get copy of the QueryBuilder.
     * @return QueryBuilder
     */
    public function cloneQueryBuilder()
    {
        return clone $this->queryBuilder;
    }


    /**
     * Get Query instance from QueryBuilder.
     * @return Query
     */
    public function getQuery()
    {
        return $this->queryBuilder->getQuery();
    }


    /**
     * Get current column mapping list.
     * @return array
     */
    public function getColumnMapping()
    {
        return $this->columnMapping;
    }


    /**
     * Get total count without applied WHERE and LIMIT.
     * @return int
     */
    public function getTotalCount()
    {
        if ($this->itemsTotalCount) {
            return $this->itemsTotalCount;
        }

        // Remove WHERE confition from QueryBuilder
        $query = $this->cloneQueryBuilder()
            ->resetDQLPart('where')
            ->setParameters([])         // May cause problems?
            ->getQuery();

        // Get total count without WHERE and LIMIT applied
        $this->itemsTotalCount = (new Paginator($query))->count();
        return $this->itemsTotalCount;
    }


    /**
     * Get every single row of the table.
     * @return array
     */
    public function fetchFullData()
    {
        return $this->cloneQueryBuilder()
            ->resetDQLPart('where')
            ->setParameters([])         // May cause problems?
            ->setMaxResults(null)
            ->setFirstResult(null)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
    }


    /**
     * Apply limit and offset.
     * @param  int  $limit   Number of rows
     * @param  int  $offset  Rows to skip
     * @return static
     */
    public function applyLimit($limit, $offset = 0)
    {
        $this->getQueryBuilder()->setMaxResults($limit)->setFirstResult($offset);
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
        $this->getQueryBuilder()->addOrderBy($this->prefixColumn($column), $sorting);
        return $this;
    }


    /**
     * Get count with applied where without limit.
     * @return int
     */
    public function count()
    {
        if (!$this->itemsCount) {
            $this->itemsCount = (new Paginator($this->getQuery()))->count();
        }

        return $this->itemsCount;
    }


    /**
     * Get first element from data.
     * @return array
     */
    public function fetch()
    {
        try {
            return $this->cloneQueryBuilder()
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult(Query::HYDRATE_ARRAY);

        } catch (NoResultException $e) {
            return [];
        }
    }


    /**
     * Get data with applied where, limit and offset.
     * @return array
     */
    public function fetchAll()
    {
        try {
            return $this->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);

        } catch (NoResultException $e) {
            return [];
        }
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


    /**
     * Add prefix to the column name.
     * @param  string  $column  Column name
     * @return string
     */
    protected function prefixColumn($column)
    {
        if (isset($this->columnMapping[$column])) {
            return $this->columnMapping[$column];
        }

        if (strpos($column, '.') !== false) {
            return $column;
        }

        return current($this->getQueryBuilder()->getRootAliases()).'.'.$column;
    }
}
