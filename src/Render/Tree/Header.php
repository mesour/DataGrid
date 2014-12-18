<?php

namespace Mesour\DataGrid\Render\Tree;

use Mesour\DataGrid\Column,
    Mesour\DataGrid\Render,
    \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Header extends Render\Header {

	public function create() {
		$tr = Html::el('div', $this->attributes);
		foreach ($this->cells as $cell) {
			$tr->add($cell->create());
		}
		return $tr;
	}

}