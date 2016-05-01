<?php

namespace Mesour\DataGridTests\Sources;

use Doctrine\ORM\QueryBuilder;
use Mesour\DataGrid\Sources\DoctrineGridSource;
use Mesour\Filter;
use Mesour\FilterTests\Sources\BaseDoctrineFilterSourceTest;
use Mesour\Sources;
use Nette\Database;

abstract class BaseDoctrineGridSourceTest extends BaseDoctrineFilterSourceTest
{

	public function __construct($setConfigFiles = true)
	{
		if ($setConfigFiles) {
			$this->configFile = __DIR__ . '/../../../config.php';
			$this->localConfigFile = __DIR__ . '/../../../config.local.php';
		}

		parent::__construct(false);
	}

	public function testApplyCustomDate()
	{
		$source = $this->createDoctrineSource(Sources\Tests\Entity\User::class, $this->user);

		DataSourceChecker::matchColumnNames($source);
	}

	public function testFetchForExport()
	{
		$source = $this->createDoctrineSource(Sources\Tests\Entity\User::class, $this->user);

		DataSourceChecker::matchFetchForExport($source, Sources\Tests\Entity\User::class);
	}

	protected function createDoctrineSource($table, QueryBuilder $queryBuilder)
	{
		return new DoctrineGridSource($table, 'id', $queryBuilder, $this->columnMapping);
	}

}
