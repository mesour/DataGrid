<?php

namespace Mesour\ArrayManage;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour ArrayManager
 */
class Container implements \Iterator, \ArrayAccess {

	private $data;

	public function __construct(array $arr) {
		$this->data = $arr;
	}

	public function offsetExists($offset) {
		return isset($this->data[$offset]);
	}

	public function offsetGet($offset) {
		return $this->data[$offset];
	}

	public function offsetSet($offset, $value) {
		$this->data[$offset] = $value;
	}

	public function offsetUnset($offset) {
		unset($this->data[$offset]);
	}

	function rewind() {
		return reset($this->data);
	}

	function current() {
		return current($this->data);
	}

	function key() {
		return key($this->data);
	}

	function next() {
		return next($this->data);
	}

	function valid() {
		return !is_null(key($this->data));
	}
}