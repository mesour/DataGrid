<?php

namespace Mesour\DataGridTests\Sources;

use Nette\Database;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../../../../vendor/mesour/sources/tests/classes/Connection.php';
require_once __DIR__ . '/../../../../vendor/mesour/sources/tests/classes/DatabaseFactory.php';
require_once __DIR__ . '/../../../../vendor/mesour/sources/tests/classes/DataSourceTestCase.php';
require_once __DIR__ . '/../../../../vendor/mesour/sources/tests/classes/BaseNetteDbSourceTest.php';
require_once __DIR__ . '/../../../../vendor/mesour/filter/tests/Mesour/FilterTests/Sources/BaseNetteDbFilterSourceTest.php';
require_once __DIR__ . '/../../../../vendor/mesour/filter/tests/Mesour/FilterTests/Sources/DataSourceChecker.php';
require_once __DIR__ . '/BaseNetteDbGridSourceTest.php';
require_once __DIR__ . '/DataSourceChecker.php';


class NetteDbDataGridSourceTest extends BaseNetteDbGridSourceTest
{

}

$test = new NetteDbDataGridSourceTest();
$test->run();
