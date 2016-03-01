<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
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

	public function __construct(array $data, array $relations = [])
	{
		parent::__construct($data, $relations);
	}

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
		if (count($this->structure) > 0) {
			foreach ($out as $key => $val) {
				$this->removeStructureDate($out[$key]);
			}
		}
		foreach ($out as $key => $val) {
			$out[$key] = $this->makeArrayHash($val);
		}
		$this->lastFetchAllResult = $out;
		return $out;
	}

	public function xx()
	{
		return $this->exportSelect;
	}

	public function getColumnNames()
	{
		if (count($this->columnNames) === 0) {
			$data = $this->fetch();
			if (!$data) {
				return [];
			}
			$item = (array)$data;
			$this->columnNames = array_keys($item);
		}
		return $this->columnNames;
	}

}
