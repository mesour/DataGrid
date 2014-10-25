<?php

namespace Mesour\ArrayManage\Changer;

use Mesour\ArrayManage\Searcher\Search,
    Mesour\ArrayManage\Translator,
    Mesour\ArrayManage\Validator;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour ArrayManager
 */
class Insert extends Search {

	private $values_array = array();

	/**
	 * @var array
	 */
	private $data_array = array();

	public function __construct(array & $data_array, $values_array) {
		parent::__construct($data_array);
		Validator::validateUnknownColumns($data_array, $values_array);
		$this->data_array = &$data_array;
		$this->values_array = $values_array;
	}

	public function execute() {
		$this->data_array[] = $this->addEmpty($this->values_array);
	}

	private function addEmpty($insert_arr) {
		$count = count(reset($this->data_array));
		if (count($insert_arr) !== $count) {
			$output = array();
			$keys = array_keys(reset($this->data_array));
			foreach ($keys as $key) {
				if (isset($insert_arr[$key])) {
					$output[$key] = $insert_arr[$key];
				} else {
					$output[$key] = NULL;
				}
			}
		}
		return $output;
	}

	public function test() {
		echo '<pre>';
		echo (new Translator(Translator::UPDATE, $this->update_array))->translate() . "\n" . $this->translate();
		echo '</pre>';
	}

}