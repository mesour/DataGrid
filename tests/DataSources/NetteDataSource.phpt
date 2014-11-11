<?php

use Tester\Assert,
    \DataGrid\NetteDbDataSource;

$container = require_once __DIR__ . '/../bootstrap.php';

class NetteDataSource extends \Test\BaseTestCase {

	CONST FULL_USER_COUNT = 20;

	private $db;

	public function __construct(Nette\DI\Container $container) {
		parent::__construct($container);
		$this->db = $this->getByType('Nette\Database\Context');
	}

	public function testTotalCount() {
		$source = new NetteDbDataSource($this->db->table('user'));

		Assert::same(self::FULL_USER_COUNT, $source->getTotalCount());
	}

	public function testLimit() {
		$source = new NetteDbDataSource($this->db->table('user'));

		$source->applyLimit(5);

		Assert::count(5, $source->fetchAll());
		Assert::count(self::FULL_USER_COUNT, $source->fetchFullData());
		Assert::count(self::FULL_USER_COUNT, $source->fetchAllForExport());
		Assert::same(self::FULL_USER_COUNT, $source->getTotalCount());
		Assert::same(self::FULL_USER_COUNT, $source->count());
	}

	public function testOffset() {
		$source = new NetteDbDataSource($this->db->table('user'));

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
		$source = new NetteDbDataSource($this->db->table('user'));

		$source->where('action = ?', 1);

		Assert::count(10, $source->fetchAll());
		Assert::count(self::FULL_USER_COUNT, $source->fetchFullData());
		Assert::count(10, $source->fetchAllForExport());
		Assert::same(self::FULL_USER_COUNT, $source->getTotalCount());
		Assert::same(10, $source->count());
	}

	public function testWhereAssoc() {
		$source = new NetteDbDataSource($this->db->table('page'));

		$source->where('action = ?', 1);


	}

}

$test = new NetteDataSource($container);
$test->run();