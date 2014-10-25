<?php

namespace DataGrid\Render\Tree;

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
		$td = Html::el('span', $attributes);
		$td->setHtml($this->column->getHeaderContent() . '<span class="separator">|</span>');
		return $td;
	}

}