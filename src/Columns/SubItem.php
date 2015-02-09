<?php

namespace Mesour\DataGrid\Column;

use \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class SubItem extends EmptyData {

	public function getBodyAttributes($data) {
		return parent::mergeAttributes($data, array('colspan' => $data));
	}

	public function getBodyContent($data) {
		return $this->option[self::TEXT];
	}

}