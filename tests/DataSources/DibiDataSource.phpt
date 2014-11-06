<?php

use Tester\Assert;

$container = require_once __DIR__ . '/../bootstrap.php';

class DibiDataSourceTest extends \Test\BaseTestCase {

	private $db;

	function __construct(Nette\DI\Container $container) {
		parent::__construct($container);
		$this->db = $this->getByType('\DibiConnection');
	}

	function testSomething() {
		Assert::true(true);
	}

}

$test = new DibiDataSourceTest($container);
$test->run();