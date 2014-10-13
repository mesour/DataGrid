<?php

namespace DataGrid\Render\Tree;

use \DataGrid\Column,
    DataGrid\Render,
    \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Renderer extends Render\Renderer{

	public function create() {
		$tree = Html::el('div', $this->attributes);

		$tree->add($this->header->create());

		$tree->add($this->body->create());

		return $tree;
	}

}