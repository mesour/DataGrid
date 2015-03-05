<?php

use Mesour\DataGrid\NetteDbDataSource;

$container = require_once __DIR__ . '/../bootstrap.php';

class NetteDataSource extends \Test\DataSourceTestCase {

	CONST FULL_USER_COUNT = 20;

	/**
	 * @var \Nette\Database\Context
	 */
	private $db;

	public function __construct(Nette\DI\Container $container) {
		parent::__construct($container);
		$this->db = $this->getByType('Nette\Database\Context');
	}

	public function testTotalCount() {
		$source = new NetteDbDataSource($this->db->table('user'));

		$this->matchTotalCount($source);
	}

	public function testLimit() {
		$source = new NetteDbDataSource($this->db->table('user'));

		$this->matchLimit($source);
	}

	public function testOffset() {
		$source = new NetteDbDataSource($this->db->table('user'));

		$this->matchOffset($source);
	}

	public function testWhere() {
		$source = new NetteDbDataSource($this->db->table('user'));

		$source->where('action = ?', 1);

		$this->matchWhere($source);
	}

	public function testEmpty() {
		$source = new NetteDbDataSource($this->db->table('empty'));

		$this->matchEmpty($source);
	}

	public function testCheckers() {
		$source = new NetteDbDataSource($this->db->table('user'));

		$this->matchCheckers($source);
	}

	public function testCustom() {
		$source = new NetteDbDataSource($this->db->table('user'));

		$this->matchCustom($source);
	}

	public function testCustomOr() {
		$source = new NetteDbDataSource($this->db->table('user'));

		$this->matchCustomOr($source);
	}

}

$test = new NetteDataSource($container);
$test->run();