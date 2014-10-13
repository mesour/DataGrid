<?php

namespace Mesour\ArrayManage;

use Mesour\ManagerException;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour ArrayManager
 */
class Validator {

	static public function validateArray(array $array) {
		if(!self::isMulti2($array)) {
			throw new ValidatorException('Array must be two dimensional.');
		}
		if(!self::haveSameKeysInTwoDimension($array)) {
			throw new ValidatorException('Array must have in second dimension same keys.');
		}
	}

	static public function validateUnknownColumns(array $array, array $column_keys) {
		$_arr = reset($array);
		if(!is_array($_arr)) {
			$_arr = $array;
		}
		$keys = array_keys($_arr);
		$checked_values = self::checkKnownKeys($keys, $column_keys);
		if($checked_values !== TRUE) {
			throw new ValidatorException('Unknown column ' . $checked_values . ' in managed array.');
		}
	}

	static public function checkKnownKeys(array $keys, array $arr) {
		$_keys = array_keys($arr);
		foreach($_keys as $key) {
			if(!in_array($key, $keys)) {
				return $key;
			}
		}
		return TRUE;
	}

	static public function haveSameKeysInTwoDimension(array $a) {
		$count = count(reset($a));
		$keys = array_keys(reset($a));
		foreach ($a as $v) {
			if(count($v) !== $count) {
				return FALSE;
			}
			if(array_keys($v) !== $keys) {
				return FALSE;
			}
		}
		return TRUE;
	}

	static public function isMulti2(array $a) {
		$valid = TRUE;
		foreach ($a as $v) {
			if (!is_array($v)) {
				$valid = FALSE;
			}
		}
		return $valid;
	}

	static public function isMulti3(array $a) {
		foreach ($a as $v) {
			if (is_array($v)) {
				foreach($v as $c) {
					if (!is_array($c)) return FALSE;
				}
			}
		}
		return TRUE;
	}

}

class ValidatorException extends ManagerException {

}