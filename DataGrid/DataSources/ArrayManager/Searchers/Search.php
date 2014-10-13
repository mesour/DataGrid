<?php

namespace Mesour\ArrayManage\Searcher;

use Mesour\ArrayManage\Container;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour ArrayManager
 */
class Search {

	/**
	 * @var Filter
	 */
	protected $filter;

	public function __construct(array $data_array) {
		$this->filter = new Filter(new Container($data_array));
	}

	public function where($column, $value, $condition, $operator = 'and') {
		if($operator === 'and') {
			$this->filter->addConditionAnd($column, $value, Condition::getInstance($condition));
		} else {
			$this->filter->addConditionOr($column, $value, Condition::getInstance($condition));
		}
		return $this;
	}

	protected function getResults() {
		$output = array();
		foreach ($this->filter as $key => $val) {
			$output[$key] = $val;
		}
		return $output;
	}

	protected function translate() {
		return $this->filter->translate();
	}

}