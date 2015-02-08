<?php
/**
 * Mesour Nette ArrayManager
 *
 * Documentation here: https://github.com/mesour/ArrayManager
 *
 * @license LGPL-3.0 and MIT License
 * @copyright (c) 2014 - 2015 Matous Nemec <matous.nemec@mesour.com>
 */

namespace Mesour;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour ArrayManager
 */
class ArrayManager {

	private $data_array;

	public function __construct(array & $data_array) {
		ArrayManage\Validator::validateArray($data_array);
		$this->data_array = & $data_array;
	}

	public function select() {
		return new ArrayManage\Searcher\Select($this->data_array);
	}

	public function update($update_array) {
		return new ArrayManage\Changer\Update($this->data_array, $update_array);
	}

	public function delete() {
		return new ArrayManage\Changer\Delete($this->data_array);
	}

	public function insert($values_array) {
		return new ArrayManage\Changer\Insert($this->data_array, $values_array);
	}

}

class ManagerException extends \Exception{

}