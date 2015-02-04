<?php

namespace Mesour\DataGrid\Extensions;

use Nette\Utils\Callback;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class SubItem extends BaseControl {

	private $callback;

	public function setCallback($callback) {
		Callback::check($callback);
		$this->callback = $callback;
	}

	public function invoke(array $args = array()) {
		return Callback::invokeArgs($this->callback, $args);
	}

}