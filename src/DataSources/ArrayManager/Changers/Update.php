<?php

namespace Mesour\ArrayManage\Changer;

use Mesour\ArrayManage\Searcher\Search,
    Mesour\ArrayManage\Translator,
    Mesour\ArrayManage\Validator;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour ArrayManager
 */
class Update extends Search {

	private $update_array = array();

	/**
	 * @var array
	 */
	private $data_array = array();

	public function __construct(array & $data_array, $update_array) {
		parent::__construct($data_array);
		Validator::validateUnknownColumns($data_array, $update_array);
		$this->data_array = &$data_array;
		$this->update_array = $update_array;
	}

	public function execute() {
		foreach ($this->getResults() as $key => $val) {
			foreach ($this->update_array as $_key => $_val) {
				$this->data_array[$key][$_key] = $_val;
			}
		}
	}

	public function test() {
		echo '<pre>';
        $translator = new Translator(Translator::UPDATE, $this->update_array);
		echo $translator->translate() . "\n" . $this->translate();
		echo '</pre>';
	}

}