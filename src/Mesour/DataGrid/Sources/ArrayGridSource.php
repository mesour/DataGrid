<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Sources;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class ArrayGridSource extends Mesour\Filter\Sources\ArrayFilterSource implements IGridSource
{

	private $columnNames = [];

	/**
	 * @var Mesour\ArrayManage\Searcher\Select
	 */
	private $exportSelect;

	private function getExportSelect()
	{
		if (!$this->exportSelect) {
			$this->exportSelect = clone $this->getSelect();
		}
		return $this->exportSelect;
	}

	public function where($column, $value = null, $condition = null, $operator = 'and')
	{
		parent::where($column, $value, $condition, $operator);
		$this->getExportSelect()->where($column, $value, $condition, $operator);
		return $this;
	}

	public function fetchForExport()
	{
		$out = $this->getExportSelect()->fetchAll();
		foreach ($out as $key => $val) {
			$this->removeStructureDate($out[$key]);
		}
		foreach ($out as $key => $val) {
			$out[$key] = $this->makeArrayHash($val);
		}
		$this->lastFetchAllResult = $out;
		return $out;
	}

	public function getColumnNames()
	{
		if (count($this->columnNames) === 0) {
			$columns = $this->getTableColumns($this->getTableName());

			$data = $this->fetch();
			if (!$data) {
				return [];
			}
			$item = (array) $data;
			$this->columnNames = array_unique(array_merge(array_keys($item), array_keys($columns)));
		}
		return $this->columnNames;
	}

}
