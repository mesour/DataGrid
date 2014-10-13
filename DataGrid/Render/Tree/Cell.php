<?php

namespace DataGrid\Render\Tree;

use \DataGrid\Column,
    DataGrid\Render,
    \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Cell extends Render\Cell{

	public function create() {
		$attributes = $this->column->getBodyAttributes($this->rowData);
		if($attributes === FALSE) {
			return '';
		}
		$td = Html::el('span', $attributes);
		$td->setHtml($this->column->getBodyContent($this->rowData));
		return $td;
	}

}