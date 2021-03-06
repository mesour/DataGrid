<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Sources;

use Doctrine;
use Mesour;
use Nette;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class DoctrineGridSource extends Mesour\Filter\Sources\DoctrineFilterSource implements IGridSource
{

	private $columnNames = [];

	public function fetchForExport()
	{
		try {
			$this->lastFetchAllResult = $this->cloneQueryBuilder()
				->setMaxResults(null)
				->setFirstResult(null)
				->getQuery()
				->getResult();

			return $this->fixResult(
				$this->getEntityArrayAsArrays($this->lastFetchAllResult)
			);
		} catch (Doctrine\ORM\NoResultException $e) {
			return [];
		}
	}

	public function getColumnNames()
	{
		if (!count($this->columnNames)) {
			$this->columnNames = array_keys($this->getTableColumns($this->getTableName()));
		}
		return $this->columnNames;
	}

}
