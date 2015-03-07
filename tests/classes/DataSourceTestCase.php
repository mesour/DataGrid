<?php

namespace Test;

use Mesour\DataGrid\IDataSource;
use Tester\Assert;

abstract class DataSourceTestCase extends BaseTestCase {

	CONST FULL_USER_COUNT = 20,
	    COLUMN_COUNT = 10,
	    ACTIVE_COUNT = 10,
	    CHECKERS_COUNT = 8,
	    CUSTOM_COUNT = 7,
	    CUSTOM_OR_COUNT = 3,
	    LIMIT = 5,
	    OFFSET = 2;

	protected function matchTotalCount(IDataSource $source) {
		Assert::same(self::FULL_USER_COUNT, $source->getTotalCount());
	}

	protected function matchLimit(IDataSource $source) {
		$source->applyLimit(self::LIMIT);

		$this->assertLimit($source);
	}

	protected function matchOffset(IDataSource $source) {
		$source->applyLimit(self::LIMIT, self::OFFSET);

		$all_data = $this->assertLimit($source);
		$first_user = reset($all_data);
		Assert::equal((string)(self::OFFSET + 1), (string)$first_user['user_id']);
	}

	protected function matchWhere(IDataSource $source) {
		$this->assertCounts($source, self::ACTIVE_COUNT);
	}

	protected function matchEmpty(IDataSource $source) {
		$this->assertCounts($source, 0, 0, 0);
	}

	protected function matchCheckers(IDataSource $source) {
		$source->applyCheckers('name', array(
		    'Peter', 'Claude', 'Alberta', 'Ian', 'Ada', 'Virgil', 'Catherine', 'Douglas'
		), 'text');

		$this->assertCounts($source, self::CHECKERS_COUNT);
	}

	protected function matchCustom(IDataSource $source) {
		$source->applyCustom('amount', array(
			'how1' => 'bigger',
			'how2' => 'smaller',
			'val1' => 1500,
			'val2' => 50000,
			'operator' => 'and'
		), 'number');

		$this->assertCounts($source, self::CUSTOM_COUNT);
	}

	protected function matchCustomOr(IDataSource $source) {
		$source->applyCustom('name', array(
		    'how1' => 'equal',
		    'how2' => 'equal',
		    'val1' => 'John',
		    'val2' => 'Peter',
		    'operator' => 'or'
		), 'number');

		$this->assertCounts($source, self::CUSTOM_OR_COUNT);
	}


	private function assertCounts(IDataSource $source, $active_count, $full = self::FULL_USER_COUNT, $columns = self::COLUMN_COUNT) {
		Assert::count($columns, $source->fetch());
		Assert::count($active_count, $source->fetchAll());
		Assert::count($full, $source->fetchFullData());
		Assert::count($active_count, $source->fetchAllForExport());
		Assert::same($full, $source->getTotalCount());
		Assert::same($active_count, $source->count());
	}

	private function assertLimit(IDataSource $source) {
		$all = $source->fetchAll();
		Assert::count(self::LIMIT, $all);
		Assert::count(self::FULL_USER_COUNT, $source->fetchFullData());
		Assert::count(self::FULL_USER_COUNT, $source->fetchAllForExport());
		Assert::same(self::FULL_USER_COUNT, $source->getTotalCount());
		Assert::same(self::LIMIT, $source->count());
		return $all;
	}

}