<?php

namespace Mesour\DataGrid\Tests\Sources;

use Mesour\DataGrid\Tests\BaseArrayGridSourceTest;
use Nette\Database;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../../vendor/mesour/sources/tests/classes/Connection.php';
require_once __DIR__ . '/../../../vendor/mesour/sources/tests/classes/DatabaseFactory.php';
require_once __DIR__ . '/../../../vendor/mesour/sources/tests/classes/DataSourceTestCase.php';
require_once __DIR__ . '/../../../vendor/mesour/sources/tests/classes/BaseArraySourceTest.php';
require_once __DIR__ . '/../../../vendor/mesour/filter/tests/classes/BaseArrayFilterSourceTest.php';
require_once __DIR__ . '/../../../vendor/mesour/filter/tests/classes/DataSourceChecker.php';
require_once __DIR__ . '/../../classes/BaseArrayGridSourceTest.php';
require_once __DIR__ . '/../../classes/DataSourceChecker.php';

class ArrayGridSourceTest extends BaseArrayGridSourceTest
{

}

$test = new ArrayGridSourceTest();
$test->run();
