<?php

namespace Mesour\ArrayManage\Searcher;

use Mesour\ArrayManage\Translator,
    Mesour\ArrayManage\Validator;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour ArrayManager
 */
class Filter extends \FilterIterator {

	private $conditions_or = array();

	private $conditions_and = array();

	public function __construct(\Iterator $iterator) {
		parent::__construct($iterator);
	}

	public function addConditionOr($key, $value, Condition $condition) {
		$this->conditions_or[] = array($key, $value, $condition);
	}

	public function getConditionsOr() {
		return $this->conditions_or;
	}

	public function addConditionAnd($key, $value, Condition $condition) {
		$this->conditions_and[] = array($key, $value, $condition);
	}

	public function getConditionsAnd() {
		return $this->conditions_and;
	}

	public function translate() {
        $translator = new Translator(Translator::WHERE, $this->conditions_and, $this->conditions_or);
		return $translator->translate();
	}

	private function applyConditions($current, $filter) {
		$match = TRUE;
		foreach ($filter->getConditionsAnd() as $value) {
			Validator::validateUnknownColumns($current, array($value[0] => NULL));
			if (!$value[2]->match($current[$value[0]], $value[1], $current)) {
				$match = FALSE;
			}
		}
		if (!$match) {
			return FALSE;
		}
		if (count($filter->getConditionsOr()) === 0) {
			return TRUE;
		}
		$match = FALSE;
		foreach ($filter->getConditionsOr() as $value) {
			Validator::validateUnknownColumns($current, array($value[0] => NULL));
			if ($value[2]->match($current[$value[0]], $value[1], $current)) {
				$match = TRUE;
			}
		}
		return $match;
	}

	public function accept() {
		return $this->applyConditions($this->current(), $this);
	}

}