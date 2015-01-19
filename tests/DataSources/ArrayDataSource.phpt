<?php

use Tester\Assert;

$container = require_once __DIR__ . '/../bootstrap.php';

class ArraySourceTest extends \Tester\TestCase {

	function testSomething() {
		Assert::true(true);
	}

}

$test = new ArraySourceTest();
$test->run();