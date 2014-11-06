<?php

use Tester\Assert;

$container = require_once __DIR__ . '/../bootstrap.php';

class DibiDataSourceTest extends \Test\BaseTestCase {

	private $connection;

	function __construct(Nette\DI\Container $container) {
		parent::__construct($container);
		$this->connection = $this->getByType('\DibiConnection');
	}

	function testSomething() {
		Assert::true(true);
	}

}

$test = new DibiDataSourceTest($container);
$test->run();