<?php

namespace DataGrid\Render\Table;

use \DataGrid\Column,
    DataGrid\Render,
    \Nette\Utils\Html;

/**
 * Description of \DataGrid\Render\Table\Body
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class Body extends Render\Body{

	public function create() {
		$tableBody = Html::el('tbody', $this->attributes);

		foreach($this->rows as $row) {
			$tableBody->add($row->create());
		}

		return $tableBody;
	}

}