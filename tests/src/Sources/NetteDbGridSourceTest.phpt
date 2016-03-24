<?php

namespace Mesour\DataGrid\Tests\Sources;

use Mesour\DataGrid\Tests\BaseNetteDbGridSourceTest;
use Nette\Database;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../../vendor/mesour/sources/tests/classes/Connection.php';
require_once __DIR__ . '/../../../vendor/mesour/sources/tests/classes/DatabaseFactory.php';
require_once __DIR__ . '/../../../vendor/mesour/sources/tests/classes/DataSourceTestCase.php';
require_once __DIR__ . '/../../../vendor/mesour/sources/tests/classes/BaseNetteDbSourceTest.php';
require_once __DIR__ . '/../../../vendor/mesour/filter/tests/classes/BaseNetteDbFilterSourceTest.php';
require_once __DIR__ . '/../../../vendor/mesour/filter/tests/classes/DataSourceChecker.php';
require_once __DIR__ . '/../../classes/BaseNetteDbGridSourceTest.php';
require_once __DIR__ . '/../../classes/DataSourceChecker.php';


class NetteDbDataGridSourceTest extends BaseNetteDbGridSourceTest
{

}

$test = new NetteDbDataGridSourceTest();
$test->run();
