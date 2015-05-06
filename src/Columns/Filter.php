<?php

namespace Mesour\DataGrid\Column;

use \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
abstract class Filter extends BaseOrdering {

	/**
	 * Possible option key
	 */
	const FILTERING = 'filtering';

	public function setFiltering($filtering) {
		$this->option[self::FILTERING] = (bool)$filtering;
		return $this;
	}

	protected function setDefaults() {
		return array_merge(parent::setDefaults(), array(
		    self::FILTERING => TRUE
		));
	}

    abstract function getTemplateFile();

}