<?php

namespace Mesour\DataGrid\Extensions;

use Nette\ComponentModel\IContainer;
use Nette\Utils\Callback;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class CallbackItem extends Item {

	public function __construct(IContainer $parent, $name, $description = NULL, $callback = NULL) {
		parent::__construct($parent, $name, $description);
		$this->setCallback($callback);
	}

	public function render($key = NULL, $rowData = NULL) {
		if (is_null($key) || is_null($rowData)) {
			return '';
		}
		return parent::invoke(array($rowData), NULL, NULL);
	}

	public function reset() {

	}

}