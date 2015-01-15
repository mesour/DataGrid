<?php

namespace Mesour\DataGrid\Column;

use \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Sortable extends Base {

	public function getHeaderAttributes() {
		$this->fixOption();
		return array('class' => 'sortable-column');
	}

	public function getHeaderContent() {
		return Html::el('b', array('class' => 'glyphicon glyphicon-move'));
	}

	public function getBodyContent($data) {
		$link = Html::el('a', array('class' => 'btn btn-sm btn-default move handler', 'href' => '#'));
		$link->add(Html::el('b', array('class' => 'glyphicon glyphicon-move')));
		return $link;
	}

}