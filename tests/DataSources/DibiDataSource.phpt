<?php

use Tester\Assert;

$container = require_once __DIR__ . '/../bootstrap.php';

class DibiDataSourceTest extends \Tester\TestCase {

	private $connection;

	function __construct(\DibiConnection $connection) {
		$this->connection = $connection;
	}

	function testSomething() {
		Assert::true(true);
	}

}

$test = new DibiDataSourceTest($container->getByType('\DibiConnection'));
$test->run();