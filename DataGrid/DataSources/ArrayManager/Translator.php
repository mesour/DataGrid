<?php

namespace Mesour\ArrayManage;

use Mesour\ManagerException;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour ArrayManager
 */
class Translator {

	const CONDITION = 'Condition',
	    WHERE = 'Where',
	    UPDATE = 'Update',
	    SELECT = 'Select',
	    DELETE = 'Delete';

	private $allowed = array(
	    self::CONDITION, self::WHERE, self::UPDATE, self::SELECT, self::DELETE
	);

	private $type;

	private $values = array();

	public function __construct($type, $value = NULL) {
		if (!in_array($type, $this->allowed)) {
			throw new ManagerException('First value must be some constant of this class.');
		}
		$this->type = $type;
		$counter = 0;
		foreach (func_get_args() as $arg) {
			$counter++;
			if ($counter === 1) {
				continue;
			}
			$this->values[] = $arg;
		}

	}

	public function translate() {
		return call_user_func(array($this, 'translate' . $this->type));
	}

	protected function translateCondition() {
		$condition_values = array(
		    Searcher\Condition::EQUAL => '=',
		    Searcher\Condition::NOT_EQUAL => '!=',
		    Searcher\Condition::SMALLER => '<',
		    Searcher\Condition::NOT_SMALLER => '>=',
		    Searcher\Condition::BIGGER => '>',
		    Searcher\Condition::NOT_BIGGER => '<=',
		    Searcher\Condition::STARTS_WITH => '%LIKE',
		    Searcher\Condition::NOT_STARTS_WITH => 'NOT %LIKE',
		    Searcher\Condition::ENDS_WITH => 'LIKE%',
		    Searcher\Condition::NOT_ENDS_WITH => 'NOT LIKE%',
		    Searcher\Condition::CONTAINS => '%LIKE%',
		    Searcher\Condition::NOT_CONTAINS => 'NOT %LIKE%',
		);
		return $condition_values[$this->values[0]];
	}

	protected function translateWhere() {
		$conditions_and = $this->values[0];
		$conditions_or = $this->values[1];

		if (empty($conditions_and) && empty($conditions_or)) {
			return '';
		}

		$output = 'WHERE (';
		foreach ($conditions_and as $key => $condition) {
			$output .= $this->addConditionContent($condition);
			if (isset($conditions_and[$key + 1]) || !empty($conditions_or)) {
				$output .= ' AND ';
			}
		}
		if (!empty($conditions_or)) {
			$output .= '(';
			foreach ($conditions_or as $key => $condition) {
				$output .= $this->addConditionContent($condition);
				if (isset($conditions_or[$key + 1])) {
					$output .= ' OR ';
				}
			}
			$output .= ')';
		}
		$output .= ')';
		return $output;
	}

	private function addConditionContent($condition) {
		$output = '';
		$output .= '[' . $condition[0] . '] ';
		$output .= $condition[2]->translate();
		$output .= ' ';
		$output .= '"' . $condition[1] . '"';
		return $output;
	}

	protected function translateSelect() {
		$column_arr = $this->values[0];
		$limit = $this->values[1];
		$offset = $this->values[2];
		$ordering = $this->values[3];
		$output = 'SELECT ';

		if (empty($column_arr)) {
			$output .= '*';
		}
		$iterator_count = 0;
		$values_count = count($column_arr);
		foreach ($column_arr as $val) {
			$output .= ($val === '*' ? $val : '[' . $val . ']') . ($iterator_count < $values_count - 1 ? ', ' : '');
			$iterator_count++;
		}
		$output .= "\n" . 'FROM ARRAY' . "\n" . '{WHERE}' . "\n";
		if (!is_null($limit)) {
			$output .= 'LIMIT ' . $limit . "\n";
		}
		if ($offset !== 0) {
			$output .= 'OFFSET ' . $offset . "\n";
		}
		if (!empty($ordering)) {
			$output .= 'ORDER BY ';
			foreach ($ordering as $key => $val) {
				$output .= '[' . $key . '] ' . $val;
				break;
			}
		}

		return $output;
	}

	protected function translateUpdate() {
		$output = 'UPDATE ARRAY VALUES(';
		$iterator_count = 0;
		$values_count = count($this->values[0]);
		foreach ($this->values[0] as $key => $val) {
			$output .= '[' . $key . '] = "' . $val . '"' . ($iterator_count < $values_count - 1 ? ', ' : '');
			$iterator_count++;
		}
		$output .= ')';
		return $output;
	}

	protected function translateDelete() {
		$output = 'DELETE ARRAY';
		return $output;
	}

}