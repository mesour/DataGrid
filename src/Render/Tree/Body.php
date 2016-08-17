<?php

namespace Mesour\DataGrid\Render\Tree;

use Mesour\DataGrid\Column,
    Mesour\DataGrid\Render,
    \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Body extends Render\Body {

	public function create() {
		$treeBody = Html::el('ul', $this->attributes);

		foreach ($this->rows as $row) {
			$treeBody->addHtml($row->create());
		}

		return $treeBody;
	}

}