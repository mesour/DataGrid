<?php
namespace Mesour\DataGrid\Tests;

use Mesour\DataGrid\Sources\IGridSource;
use Mesour\Sources\Tests\DataSourceTestCase;
use Tester\Assert;

class DataSourceChecker
{

	const FILTERED_COUNT = 2;

	public static function matchColumnNames(IGridSource $source)
	{
		$expected = self::getColumnNames();
		sort($expected);

		$actual = $source->getColumnNames();
		sort($actual);

		Assert::equal($expected, $actual);
	}

	private static function getColumnNames()
	{
		return [
			'id',
			'action',
			'group_id',
			'role',
			'name',
			'surname',
			'email',
			'last_login',
			'amount',
			'avatar',
			'order',
			'timestamp',
			'has_pro',
		];
	}

	public static function matchFetchForExport(IGridSource $source, $rawClassType)
	{
		$limit = 1;

		$source->applyCheckers('name', ['John'], 'text');
		$source->applyLimit($limit);

		self::matchCounts($source, $source->fetchForExport(), self::FILTERED_COUNT, $rawClassType);
		self::matchCounts($source, $source->fetchFullData(), DataSourceTestCase::FULL_USER_COUNT, $rawClassType);
		self::matchCounts($source, $source->fetchAll(), $limit, $rawClassType);
	}

	protected static function matchCounts(IGridSource $source, array $result, $expectedCount, $rawClassType)
	{
		Assert::count($expectedCount, $result);

		$lastRows = $source->fetchLastRawRows();
		if (count($lastRows) > 0) {
			Assert::type($rawClassType, reset($lastRows));
		}
		Assert::count($expectedCount, $lastRows);
	}

}
