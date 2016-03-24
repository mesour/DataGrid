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
use Nette;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class NetteDbGridSource extends Mesour\Filter\Sources\NetteDbFilterSource implements IGridSource
{

	private $columnNames = [];

	public function fetchForExport()
	{
		$selection = $this->getSelection(false);
		$this->lastFetchAllResult = [];
		$out = [];
		foreach ($selection->fetchAll() as $row) {
			/** @var Nette\Database\Table\ActiveRow $row */
			$this->lastFetchAllResult[] = $row;
			$out[] = $this->makeArrayHash($row->toArray());
		}
		return $out;
	}

	public function getColumnNames()
	{
		if (!count($this->columnNames)) {
			$data = $this->fetch();
			if (!$data) {
				return [];
			}
			$this->columnNames = array_keys((array) $data);
		}
		return $this->columnNames;
	}

}
