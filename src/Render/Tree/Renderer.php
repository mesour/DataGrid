<?php

namespace Mesour\DataGrid\Render\Tree;

use Mesour\DataGrid\Column,
    Mesour\DataGrid\Render,
    \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Renderer extends Render\Renderer {

	public function create() {
		$tree = Html::el('div', $this->attributes);

		$tree->addHtml($this->header->create());

		$tree->addHtml($this->body->create());

		return $tree;
	}

}