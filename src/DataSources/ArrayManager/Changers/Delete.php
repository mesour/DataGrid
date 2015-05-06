<?php

namespace Mesour\ArrayManage\Changer;

use Mesour\ArrayManage\Searcher\Search,
	Mesour\ArrayManage\Translator;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour ArrayManager
 */
class Delete extends Search {

	/**
	 * @var array
	 */
	private $data_array = array();

	public function __construct(array & $data_array) {
		parent::__construct($data_array);
		$this->data_array = & $data_array;
	}

	public function execute() {
		foreach($this->getResults() as $key => $val) {
			unset($this->data_array[$key]);
		}
	}

	public function test() {
		echo '<pre>';
        $translator = new Translator(Translator::DELETE);
		echo $translator->translate() . "\n" . $this->translate();
		echo '</pre>';
	}

}