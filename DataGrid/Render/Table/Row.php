<?php

namespace DataGrid\Render\Table;

use \DataGrid\Column,
    DataGrid\Render,
    \Nette\Utils\Html;

/**
 * Description of \DataGrid\Render\Table\Cell
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class Row extends Render\Row{

	public function create() {
		$tr = Html::el('tr', $this->attributes);
		foreach($this->cells as $cell) {
			$tr->add($cell->create());
		}
		return $tr;
	}

}