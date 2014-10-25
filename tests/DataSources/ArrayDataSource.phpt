<?php

use Tester\Assert;

$container = require_once __DIR__ . '/../bootstrap.php';

class ArraySourceTest extends \Tester\TestCase {

	private $container;

	function __construct(Nette\DI\Container $container) {
		$this->container = $container;
	}

	function testSomething() {
		Assert::true(true);
	}

}

$test = new ArraySourceTest($container);
$test->run();