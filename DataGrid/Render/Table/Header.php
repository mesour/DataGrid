<?php

namespace DataGrid\Render\Table;

use \DataGrid\Column,
    DataGrid\Render,
    \Nette\Utils\Html;

/**
 * Description of \DataGrid\Render\Table\Header
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class Header extends Render\Header{

	public function create() {
		$tableHead = Html::el('thead', $this->attributes);
		$tr = Html::el('tr', $this->attributes);
		foreach($this->cells as $cell) {
			$tr->add($cell->create());
		}
		$tableHead->add($tr);
		return $tableHead;
	}

}