<?php

namespace Mesour\DataGrid\Render\Table;

use Mesour\DataGrid\Column,
    Mesour\DataGrid\Render,
    \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Row extends Render\Row {

	public function create() {
		$tr = Html::el('tr', $this->attributes);
		foreach ($this->cells as $cell) {
			$tr->add($cell->create());
		}
		return $tr;
	}

}