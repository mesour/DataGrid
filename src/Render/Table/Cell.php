<?php

namespace Mesour\DataGrid\Render\Table;

use Mesour\DataGrid\Column,
    Mesour\DataGrid\Render,
    \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Cell extends Render\Cell {

	public function create() {
		$attributes = $this->column->getBodyAttributes($this->rowData);
		if ($attributes === FALSE) {
			return '';
		}
		$td = Html::el('td', $attributes);
		$content = $this->column->getBodyContent($this->rowData);

		if(!is_null($content)) {
			$td->setHtml($content);
		}
		return $td;
	}

}