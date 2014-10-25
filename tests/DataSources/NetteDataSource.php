<?php

use Tester\Assert;

$container = require_once __DIR__ . '/../bootstrap.php';

class NetteDataSource extends \Tester\TestCase {

	private $container;

	function __construct(Nette\DI\Container $container) {
		$this->container = $container;
	}

	function testTotalCount() {
		$database = $this->container->getByType('Nette\Database\Context');

		$source = new \DataGrid\NetteDbDataSource($database->table('user'));

		Assert::same(19, $source->getTotalCount());
	}

	function testLimit() {
		$database = $this->container->getByType('Nette\Database\Context');

		$source = new \DataGrid\NetteDbDataSource($database->table('user'));

		$source->applyLimit(5);

		Assert::count(5, $source->fetchAll());
		Assert::count(19, $source->fetchFullData());
		Assert::count(19, $source->fetchAllForExport());
		Assert::same(19, $source->getTotalCount());
		Assert::same(19, $source->count());
	}

	function testOffset() {
		$database = $this->container->getByType('Nette\Database\Context');

		$source = new \DataGrid\NetteDbDataSource($database->table('user'));

		$source->applyLimit(5, 2);

		$all_data = $source->fetchAll();
		$first_user = reset($all_data);

		Assert::count(5, $all_data);
		Assert::count(19, $source->fetchFullData());
		Assert::count(19, $source->fetchAllForExport());
		Assert::equal('3', (string) $first_user['user_id']);
		Assert::same(19, $source->getTotalCount());
		Assert::same(19, $source->count());
	}

	function testWhere() {
		$database = $this->container->getByType('Nette\Database\Context');

		$source = new \DataGrid\NetteDbDataSource($database->table('user'));

		$source->where('action = ?', 1);

		Assert::count(11, $source->fetchAll());
		Assert::count(19, $source->fetchFullData());
		Assert::count(11, $source->fetchAllForExport());
		Assert::same(19, $source->getTotalCount());
		Assert::same(11, $source->count());
	}

}

$test = new NetteDataSource($container);
$test->run();