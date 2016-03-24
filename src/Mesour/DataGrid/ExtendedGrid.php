<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 *
 * @method null onEditCell($lineId, $columnName, $newValue, $oldValue, $dataValue = null)
 * @method null onSort($data, $itemId)
 * @method null onFilter(Extensions\Filter\IFilter $filter)
 */
abstract class ExtendedGrid extends BaseGrid
{

	const FILTER_WRAPPER = 'filter-wrapper',
		PAGER_WRAPPER = 'pager-wrapper';

	static public $defaults = [
		self::WRAPPER => [
			'el' => 'div',
			'attributes' => [
				'class' => 'mesour-datagrid',
			],
		],
		self::PAGER_WRAPPER => [
			'el' => 'div',
			'attributes' => [
				'class' => 'mesour-datagrid-pager',
			],
		],
		self::FILTER_WRAPPER => [
			'el' => 'div',
			'attributes' => [
				'class' => 'mesour-datagrid-filter',
			],
		],
	];

	public function __construct($name = null, Mesour\Components\ComponentModel\IContainer $parent = null)
	{
		parent::__construct($name, $parent);
		$this->option = self::$defaults;
	}

	/** @var Mesour\Components\Utils\Html */
	protected $pagerWrapper;

	/** @var Mesour\Components\Utils\Html */
	protected $filterWrapper;

	public $onSort = [];

	public $onEditCell = [];

	public $onFilter = [];

	/**
	 * @param int $pageLimit
	 * @return Extensions\Pager\IPager
	 */
	public function enablePager($pageLimit = 20)
	{
		$pager = $this->getExtension('IPager');
		/** @var Extensions\Pager\IPager $pager */
		$pager->getPaginator()->setItemsPerPage($pageLimit);
		return $pager;
	}

	/**
	 * @param string $cacheDir
	 * @param string|null $fileName
	 * @return Extensions\Export\IExport
	 */
	public function enableExport($cacheDir, $fileName = null)
	{
		return $this->getExtension('IExport')
			->setCacheDir($cacheDir)
			->setFileName($fileName);
	}

	/**
	 * @param bool $inline
	 * @return Extensions\Filter\IFilter
	 */
	public function enableFilter($inline = true)
	{
		return $this->getExtension('IFilter')->setInline($inline);
	}

	/**
	 * @return Extensions\Selection\ISelection
	 * @throws Mesour\InvalidArgumentException
	 */
	public function enableRowSelection()
	{
		return $this->getExtension('ISelection');
	}

	/**
	 * @param string $columnName
	 * @return Extensions\Sortable\ISortable
	 * @throws Mesour\InvalidArgumentException
	 */
	public function enableSortable($columnName)
	{
		return $this->getExtension('ISortable')
			->setColumnName($columnName);
	}

	/**
	 * @return Extensions\Editable\IEditable
	 */
	public function enableEditable()
	{
		return $this->getExtension('IEditable');
	}

	public function getPagerPrototype()
	{
		return $this->pagerWrapper
			? $this->pagerWrapper
			: ($this->pagerWrapper = Mesour\Components\Utils\Html::el(
				$this->option[self::PAGER_WRAPPER]['el'],
				$this->option[self::PAGER_WRAPPER]['attributes']
			));
	}

	public function getFilterPrototype()
	{
		return $this->filterWrapper
			? $this->filterWrapper
			: ($this->filterWrapper = Mesour\Components\Utils\Html::el(
				$this->option[self::FILTER_WRAPPER]['el'],
				$this->option[self::FILTER_WRAPPER]['attributes']
			));
	}

	public function create($data = [])
	{
		$filter = $this->getExtension('IFilter', false);
		$this->onRenderColumnHeader[] = function (Column\IColumn $column, $i, $columnCount) use ($filter) {
			if ($i + 1 === $columnCount && $filter instanceof Extensions\Filter\IFilter) {
				$column->setFilterReset($filter);
			}
		};
		return parent::create($data);
	}

	public function __clone()
	{
		$this->pagerWrapper = null;
		$this->filterWrapper = null;
		parent::__clone();
	}

}
