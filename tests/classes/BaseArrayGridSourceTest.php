<?php

namespace Mesour\DataGrid\Tests;

use Mesour\DataGrid\Sources\ArrayGridSource;
use Mesour\Filter;
use Mesour\Sources\ArrayHash;
use Nette\Database;

abstract class BaseArrayGridSourceTest extends Filter\Tests\BaseArrayFilterSourceTest
{

	public function __construct($setConfigFiles = true)
	{
		if ($setConfigFiles) {
			$this->configFile = __DIR__ . '/../config.php';
			$this->localConfigFile = __DIR__ . '/../config.local.php';
		}

		parent::__construct(false);
	}

	public function testColumnNames()
	{
		$source = new ArrayGridSource('users', 'id', self::$user);

		DataSourceChecker::matchColumnNames($source);
	}

	public function testFetchForExport()
	{
		$source = new ArrayGridSource('users', 'id', self::$user);

		DataSourceChecker::matchFetchForExport($source, ArrayHash::class);
	}

}
