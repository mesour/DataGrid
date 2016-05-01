<?php

namespace Mesour\DataGridTests\Sources;

use Nette\Database;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../../../../vendor/mesour/sources/tests/classes/Connection.php';
require_once __DIR__ . '/../../../../vendor/mesour/sources/tests/classes/DatabaseFactory.php';
require_once __DIR__ . '/../../../../vendor/mesour/sources/tests/classes/DataSourceTestCase.php';
require_once __DIR__ . '/../../../../vendor/mesour/sources/tests/classes/BaseArraySourceTest.php';
require_once __DIR__ . '/../../../../vendor/mesour/filter/tests/Mesour/FilterTests/Sources/BaseArrayFilterSourceTest.php';
require_once __DIR__ . '/../../../../vendor/mesour/filter/tests/Mesour/FilterTests/Sources/DataSourceChecker.php';
require_once __DIR__ . '/BaseArrayGridSourceTest.php';
require_once __DIR__ . '/DataSourceChecker.php';

class ArrayGridSourceTest extends BaseArrayGridSourceTest
{

}

$test = new ArrayGridSourceTest();
$test->run();
