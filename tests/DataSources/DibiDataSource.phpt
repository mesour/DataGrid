<?php

use Tester\Assert;

$container = require_once __DIR__ . '/../bootstrap.php';

class DibiDataSourceTest extends \Tester\TestCase {

	private $container;

	function __construct(Nette\DI\Container $container) {
		$this->container = $container;
		print_r($container->getByType('\DibiConnection'));
		die;
	}

	function testSomething() {
		Assert::true(true);
	}

}

$test = new DibiDataSourceTest($container);
$test->run();