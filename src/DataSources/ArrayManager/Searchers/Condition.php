<?php

namespace Mesour\ArrayManage\Searcher;

use Mesour\ManagerException,
    Mesour\ArrayManage\Translator;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour ArrayManager
 */
class Condition {

	const EQUAL = 'Equal',
	    NOT_EQUAL = 'NotEqual',
	    SMALLER = 'Smaller',
	    NOT_SMALLER = 'NotSmaller',
	    BIGGER = 'Bigger',
	    NOT_BIGGER = 'NotBigger',
	    STARTS_WITH = 'StartsWith',
	    NOT_STARTS_WITH = 'NotStartsWith',
	    ENDS_WITH = 'EndsWith',
	    NOT_ENDS_WITH = 'NotEndsWith',
	    CONTAINS = 'Contains',
	    NOT_CONTAINS = 'NotContains';

	static private $case_sensitive = FALSE;

	private $allowed = array(
	    self::EQUAL, self::NOT_EQUAL,
	    self::SMALLER, self::NOT_SMALLER,
	    self::BIGGER, self::NOT_BIGGER,
	    self::STARTS_WITH, self::NOT_STARTS_WITH,
	    self::ENDS_WITH, self::NOT_ENDS_WITH,
	    self::CONTAINS, self::NOT_CONTAINS,
	);

	private $matcher;

	public function __construct($matcher) {
		if (!in_array($matcher, $this->allowed) && !is_callable($matcher)) {
			throw new ManagerException('First value must be callable or some constant of this class.');
		}
		$this->matcher = $matcher;
	}

	static public function getInstance($matcher) {
		return new self($matcher);
	}

	static public function setKeysSensitive($sensitive = TRUE) {
		self::$case_sensitive = $sensitive;
	}

	public function match($value, $searched_value, $row_data) {
		if (is_string($this->matcher)) {
			return call_user_func(array($this, 'match' . $this->matcher), $value, $searched_value, $row_data);
		} else {
			return call_user_func($this->matcher, $value, $searched_value, $row_data);
		}
	}

	public function translate() {
		return (new Translator(Translator::CONDITION, $this->matcher))->translate();
	}

	private function matchEqual($value, $searched_value) {
		if(!self::$case_sensitive) {
			$searched_value = strtolower($searched_value);
			$value = strtolower($value);
		}
		return (is_null($searched_value) ? is_null($value) : ((string)$searched_value === (string)$value));
	}

	private function matchNotEqual($value, $searched_value) {
		return !$this->matchEqual($value, $searched_value);
	}

	private function matchSmaller($value, $searched_value) {
		if(!self::$case_sensitive) {
			$searched_value = strtolower($searched_value);
			$value = strtolower($value);
		}
		return ($value < $searched_value);
	}

	private function matchNotSmaller($value, $searched_value) {
		return !$this->matchSmaller($value, $searched_value);
	}

	private function matchBigger($value, $searched_value) {
		if(!self::$case_sensitive) {
			$searched_value = strtolower($searched_value);
			$value = strtolower($value);
		}
		return ($value > $searched_value);
	}

	private function matchNotBigger($value, $searched_value) {
		return !$this->matchBigger($value, $searched_value);
	}

	private function matchStartsWith($value, $searched_value) {
		if(!self::$case_sensitive) {
			$searched_value = strtolower($searched_value);
			$value = strtolower($value);
		}
		return strncmp($value, $searched_value, strlen($searched_value)) === 0;
	}

	private function matchNotStartsWith($value, $searched_value) {
		return !$this->matchStartsWith($value, $searched_value);
	}

	private function matchEndsWith($value, $searched_value) {
		if(!self::$case_sensitive) {
			$searched_value = strtolower($searched_value);
			$value = strtolower($value);
		}
		return strlen($value) === 0 || substr($value, -strlen($searched_value)) === $searched_value;
	}

	private function matchNotEndsWith($value, $searched_value) {
		return !$this->matchEndsWith($value, $searched_value);
	}

	private function matchContains($value, $searched_value) {
		if(!self::$case_sensitive) {
			$searched_value = strtolower($searched_value);
			$value = strtolower($value);
		}
		return strpos($value, $searched_value) !== FALSE;
	}

	private function matchNotContains($value, $searched_value) {
		return !$this->matchContains($value, $searched_value);
	}
}