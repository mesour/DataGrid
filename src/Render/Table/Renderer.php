<?php

namespace Mesour\DataGrid\Render\Table;

use Mesour\DataGrid\Column,
    Mesour\DataGrid\Render,
    \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Renderer extends Render\Renderer {

	public function create() {
		$table = Html::el('table', $this->attributes);

		$table->add($this->header->create());

		$table->add($this->body->create());

		return $table;
	}

}