<?php

namespace DataGrid\Render\Table;

use \DataGrid\Column,
    DataGrid\Render,
    \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class HeaderCell extends Render\HeaderCell{

	public function create() {
		$attributes = $this->column->getHeaderAttributes();
		if($attributes === FALSE) {
			return '';
		}
		$td = Html::el('th', $attributes);
		$td->setHtml($this->column->getHeaderContent());
		return $td;
	}

}