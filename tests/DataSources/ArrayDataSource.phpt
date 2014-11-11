<?php

use Tester\Assert;

$container = require_once __DIR__ . '/../bootstrap.php';

class ArraySourceTest extends \Test\BaseTestCase {

	function testSomething() {
		Assert::true(true);
	}

}

$test = new ArraySourceTest($container);
$test->run();