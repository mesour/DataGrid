<?php

namespace DataGrid\Column;

use \Nette\Utils\Html;

/**
 * Description of \DataGrid\Column\Sortable
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class Sortable extends Base {

	/**
	 * Create HTML header
	 * 
	 * @return \Nette\Utils\Html
	 */
	public function createHeader() {
		parent::createHeader();

		$th = Html::el('th', array('class' => 'act buttons-count-1'));
		$th->add(Html::el('b', array('class' => 'glyphicon glyphicon-move')));
		return $th;
	}

	/**
	 * Create HTML body
	 *
	 * @param null $data
	 * @param string $container
	 * @return Html|string|void
	 * @throws Grid_Exception
	 */
	public function createBody($data = NULL, $container = 'td') {
		parent::createBody($data);

		$td = Html::el($container);

		$link = Html::el('a', array('class' => 'btn btn-sm btn-default move handler', 'href' => '#'));
		$link->add(Html::el('b', array('class' => 'glyphicon glyphicon-move')));
		$td->add($link);
		return $td;
	}

}