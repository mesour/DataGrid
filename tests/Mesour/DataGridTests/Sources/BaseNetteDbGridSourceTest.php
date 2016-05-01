<?php

namespace Mesour\DataGridTests\Sources;

use Mesour\DataGrid\Sources\NetteDbGridSource;
use Mesour\Filter;
use Mesour\FilterTests\Sources\BaseNetteDbFilterSourceTest;
use Nette\Database;

abstract class BaseNetteDbGridSourceTest extends BaseNetteDbFilterSourceTest
{

	public function __construct($setConfigFiles = true)
	{
		if ($setConfigFiles) {
			$this->configFile = __DIR__ . '/../../../config.php';
			$this->localConfigFile = __DIR__ . '/../../../config.local.php';
		}

		parent::__construct(false);
	}

	public function testColumnNames()
	{
		$source = new NetteDbGridSource($this->tableName, 'id', $this->user, $this->context, $this->columnMapping);

		DataSourceChecker::matchColumnNames($source);
	}

	public function testFetchForExport()
	{
		$source = new NetteDbGridSource($this->tableName, 'id', $this->user, $this->context, $this->columnMapping);

		DataSourceChecker::matchFetchForExport($source, Database\Table\ActiveRow::class);
	}

}
