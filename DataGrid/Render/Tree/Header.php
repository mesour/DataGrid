<?php

namespace DataGrid\Render\Tree;

use \DataGrid\Column,
    DataGrid\Render,
    \Nette\Utils\Html;

/**
 * Description of \DataGrid\Render\Tree\Header
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class Header extends Render\Header{

	public function create() {
		$tr = Html::el('div', $this->attributes);
		foreach($this->cells as $cell) {
			$tr->add($cell->create());
		}
		return $tr;
	}

}