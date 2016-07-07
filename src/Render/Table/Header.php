<?php

namespace Mesour\DataGrid\Render\Table;

use Mesour\DataGrid\Column,
    Mesour\DataGrid\Render,
    \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Header extends Render\Header {

	public function create() {
		$tableHead = Html::el('thead', $this->header_attributes);
		$tr = Html::el('tr', $this->attributes);
		foreach ($this->cells as $cell) {
			$tr->addHtml($cell->create());
		}
		$tableHead->addHtml($tr);
		return $tableHead;
	}

}