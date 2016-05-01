<?php
namespace Mesour\DataGridTests\Sources;

use Mesour\DataGrid\Sources\ArrayGridSource;
use Mesour\DataGrid\Sources\DoctrineGridSource;
use Mesour\DataGrid\Sources\IGridSource;
use Mesour\DataGrid\Sources\NetteDbGridSource;
use Mesour\InvalidArgumentException;
use Mesour\Sources\Tests\DataSourceTestCase;
use Tester\Assert;

class DataSourceChecker
{

	const FILTERED_COUNT = 2;

	public static function matchColumnNames(IGridSource $source)
	{
		$expected = self::getColumnNames($source);
		sort($expected);

		$actual = $source->getColumnNames();
		sort($actual);

		Assert::equal($expected, $actual);
	}

	private static function getColumnNames(IGridSource $source)
	{
		if ($source instanceof NetteDbGridSource) {
			return [
				'action',
				'amount',
				'avatar',
				'companies',
				'email',
				'group_id',
				'groups',
				'has_pro',
				'id',
				'last_login',
				'name',
				'order',
				'role',
				'surname',
				'timestamp',
				'user_addresses',
				'wallet_id',
				'wallets',
			];
		} elseif ($source instanceof ArrayGridSource) {
			return [
				'action',
				'amount',
				'avatar',
				'email',
				'group_id',
				'has_pro',
				'id',
				'last_login',
				'name',
				'order',
				'role',
				'surname',
				'timestamp',
				'wallet_id',
			];
		} elseif ($source instanceof DoctrineGridSource) {
			return [
				'action',
				'addresses',
				'amount',
				'avatar',
				'companies',
				'email',
				'group',
				'group_id',
				'has_pro',
				'id',
				'last_login',
				'name',
				'order',
				'role',
				'surname',
				'timestamp',
				'wallet',
				'wallet_id',
			];
		} else {
			throw new InvalidArgumentException('Unknown source');
		}
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
