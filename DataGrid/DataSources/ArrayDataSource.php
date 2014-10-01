<?php

namespace DataGrid;

/**
 * Default data source for data grid
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class ArrayDataSource implements IDataSource {

	/**
	 * Data array
	 *
	 * @var Array
	 */
	private $data = array();

	/**
	 * Data count
	 *
	 * @var Integer
	 */
	private $count = 0;

	/**
	 * Array with where conditions
	 *
	 * @var Array 
	 */
	private $where_arr = array();
	
	/**
	 * Operators => Name of operator function
	 *
	 * @var Array
	 */
	private $operators = array(
	    '=' => 'Equal',
	    '!=' => 'NotEqual',
	    '<' => 'Smaller',
	    '>' => 'Bigger',
	    '<=' => 'BiggerEqual',
	    '>=' => 'BiggerEqual',
	);
	
	/**
	 * Limit on array
	 *
	 * @var Integer|NULL
	 */
	private $limit = NULL;
	
	/**
	 * Offset on array
	 *
	 * @var Integer|NULL
	 */
	private $offset = 0;

	/**
	 * Create instance
	 * 
	 * @param Array $data
	 */
	public function __construct(array $data) {
		$this->count = count($data);
		$this->data = $data;
	}

	/**
	 * Get array data count
	 * 
	 * @return Integer
	 */
	public function getTotalCount() {
		return $this->count;
	}

	/**
	 * Add where condition
	 * 
	 * Example with using match callback: <pre>
	 *	function myMatch($value, $searched_value, $row_data){
	 *		return ($value === $searched_value || $searched_value === $row_data['id']);
	 *	}
	 * 
	 *	Using:
	 *	
	 *	$this->where('{column_key}', 'Searched value', 'myMatch');
	 * </pre>
	 * 
	 * @param String $column
	 * @param Mixed $value
	 * @param Mixed $operator Operators "=|!=|>=|>|<|<=" or callable callback
	 * @param String $combination AND or OR
	 */
	public function where($column, $value = NULL, $operator = '=', $combination = 'AND') {
		$this->where_arr[$column] = array(
		    'column' => $column,
		    'value' => $value,
		    'operator' => $operator,
		    'combination' => $combination,
		);
	}

	/**
	 * Apply limit and offset
	 * 
	 * @param Integer $limit
	 * @param Integer $offset
	 */
	public function applyLimit($limit, $offset = 0) {
		$this->limit = $limit;
		$this->offset = $offset;
	}

	/**
	 * Get count after applied where
	 * 
	 * @return Integer
	 */
	public function count() {
		return count($this->getAppliedSearched());
	}

	/**
	 * Get searched values witp applied limit, offset and where
	 * 
	 * @return Array
	 */
	public function fetchAll() {
		return array_slice($this->getAppliedSearched(), $this->offset, $this->limit);
	}
	
	
	/**
	 * Return first element from data
	 * 
	 * @return Array
	 */
	public function fetch() {
		return $this->getAppliedSearched(TRUE);
	}

	/**
	 * Apply matches to data
	 * 
	 * @param Bool $first_element Set to true if need only first element
	 * 
	 * @return Array
	 */
	private function getAppliedSearched($first_element = FALSE) {
		$output = array();
		foreach($this->getMatches() as $key => $searched) {
			$all_matched = TRUE;
			$one_matched = FALSE;
			$is_or = FALSE;
			foreach($searched as $where_key => $matched) {
				$where = $this->where_arr[$where_key];
				if($where['combination'] === 'OR') {
					$is_or = TRUE;
				}
				if(!$matched) {
					$all_matched = FALSE;
				}elseif($matched) {
					$one_matched = TRUE;
				}
			}
			if($all_matched || ($is_or && $one_matched)) {
				if($first_element) {
					return $this->data[$key];
				} else {
					$output[] = $this->data[$key];
				}
			}
			
		}
		return $output;
	}

	/**
	 * Get array with matches
	 * 
	 * @return Array
	 */
	private function getMatches() {
		$searched = array();
		foreach ($this->data as $key => $data_arr) {
			$searched[$key] = array();
			foreach ($this->where_arr as $where_key => $where) {
				if(isset($this->operators[$where['operator']])) {
					$method_name = 'match' . $this->operators[$where['operator']];
				} else {
					$method_name = $where['operator'];
				}
				if (method_exists($this, $method_name)) {
					$matched = $this->{$method_name}($data_arr[$where['column']],  $where['value']);
				} else {
					$matched = call_user_func_array($method_name, array(
					    $data_arr[$where['column']],
					    $where['value'],
					    $data_arr
					));
				}
				$searched[$key][$where_key] = $matched;
			}
		}
		return $searched;
	}
	
	/**
	 * Match equal
	 * 
	 * @param Mixed $value
	 * @param Mixed $searched_value
	 * @return Bool
	 */
	private function matchEqual($value, $searched_value) {
		return (is_null($searched_value) ? is_null($value) : (strtolower($searched_value) == strtolower($value)));
	}
	
	/**
	 * Match not equal
	 * 
	 * @param Mixed $value
	 * @param Mixed $searched_value
	 * @return Bool
	 */
	private function matchNotEqual($value, $searched_value) {
		return (is_null($searched_value) ? !is_null($value) : (strtolower($searched_value) != strtolower($value)));
	}
	
	/**
	 * Match smaller
	 * 
	 * @param Mixed $value
	 * @param Mixed $searched_value
	 * @return Bool
	 */
	private function matchSmaller($value, $searched_value) {
		return ($value < $searched_value);
	}
	
	/**
	 * Match bigger
	 * 
	 * @param Mixed $value
	 * @param Mixed $searched_value
	 * @return Bool
	 */
	private function matchBigger($value, $searched_value) {
		return ($value > $searched_value);
	}
	
	/**
	 * Match smaller equal
	 * 
	 * @param Mixed $value
	 * @param Mixed $searched_value
	 * @return Bool
	 */
	private function matchSmallerEqual($value, $searched_value) {
		return ($value <= $searched_value);
	}
	
	/**
	 * Match bigger equal
	 * 
	 * @param Mixed $value
	 * @param Mixed $searched_value
	 * @return Bool
	 */
	private function matchBiggerEqual($value, $searched_value) {
		return ($value >= $searched_value);
	}

}
