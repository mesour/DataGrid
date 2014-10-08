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
class Cell extends Render\Cell{

	public function create() {
		$attributes = $this->column->getBodyAttributes($this->rowData);
		if($attributes === FALSE) {
			return '';
		}
		$td = Html::el('td', $attributes);
		$td->setHtml($this->column->getBodyContent($this->rowData));
		return $td;
	}

}