<?php

use Tester\Assert,
    \DataGrid\NetteDbDataSource;

$container = require_once __DIR__ . '/../bootstrap.php';

class NetteDataSource extends \Tester\TestCase {

	CONST FULL_USER_COUNT = 20;

	private $database;

	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}

	public function testTotalCount() {
		$source = new NetteDbDataSource($this->database->table('user'));

		Assert::same(self::FULL_USER_COUNT, $source->getTotalCount());
	}

	public function testLimit() {
		$source = new NetteDbDataSource($this->database->table('user'));

		$source->applyLimit(5);

		Assert::count(5, $source->fetchAll());
		Assert::count(self::FULL_USER_COUNT, $source->fetchFullData());
		Assert::count(self::FULL_USER_COUNT, $source->fetchAllForExport());
		Assert::same(self::FULL_USER_COUNT, $source->getTotalCount());
		Assert::same(self::FULL_USER_COUNT, $source->count());
	}

	public function testOffset() {
		$source = new NetteDbDataSource($this->database->table('user'));

		$source->applyLimit(5, 2);

		$all_data = $source->fetchAll();
		$first_user = reset($all_data);

		Assert::count(5, $all_data);
		Assert::count(self::FULL_USER_COUNT, $source->fetchFullData());
		Assert::count(self::FULL_USER_COUNT, $source->fetchAllForExport());
		Assert::equal('3', (string) $first_user['user_id']);
		Assert::same(self::FULL_USER_COUNT, $source->getTotalCount());
		Assert::same(self::FULL_USER_COUNT, $source->count());
	}

	public function testWhere() {
		$source = new NetteDbDataSource($this->database->table('user'));

		$source->where('action = ?', 1);

		Assert::count(10, $source->fetchAll());
		Assert::count(self::FULL_USER_COUNT, $source->fetchFullData());
		Assert::count(10, $source->fetchAllForExport());
		Assert::same(self::FULL_USER_COUNT, $source->getTotalCount());
		Assert::same(10, $source->count());
	}

	public function testWhereAssoc() {
		$source = new NetteDbDataSource($this->database->table('page'));

		$source->where('action = ?', 1);


	}

}

$test = new NetteDataSource($container->getByType('Nette\Database\Context'));
$test->run();