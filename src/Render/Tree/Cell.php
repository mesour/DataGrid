<?php

namespace Mesour\DataGrid\Render\Tree;

use Mesour\DataGrid\Column,
    Mesour\DataGrid\Render,
    \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Cell extends Render\Cell {

	public function create() {
		$this->column->onRender($this->rowData, $this->column);
		$attributes = $this->column->getBodyAttributes($this->rowData);
		if ($attributes === FALSE) {
			return '';
		}
		$td = Html::el('span', $attributes);
		$td->setHtml($this->column->getBodyContent($this->rowData));
		return $td;
	}

}