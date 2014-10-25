<?php

namespace DataGrid\Render\Tree;

use \DataGrid\Column,
    DataGrid\Render,
    \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Body extends Render\Body{

	public function create() {
		$treeBody = Html::el('ul', $this->attributes);

		foreach($this->rows as $row) {
			$treeBody->add($row->create());
		}

		return $treeBody;
	}

}