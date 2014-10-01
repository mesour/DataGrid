<?php

namespace DataGrid;

use \Nette\Utils\Html,
    \Nette\Application\UI\Presenter;

/**
 * Description of \DataGrid\SortableColumn
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class SortableColumn extends BaseColumn {

	/**
	 * @param \Nette\Application\UI\Presenter
	 * @param array $option
	 */
	public function __construct(Presenter $presenter) {
		parent::__construct($presenter, array());
	}

	/**
	 * Create HTML header
	 * 
	 * @return \Nette\Utils\Html
	 * @throws \DataGrid\Grid_Exception
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

		$link = Html::el('a', array('class' => 'btn btn-default move handler', 'href' => '#'));
		$link->add(Html::el('b', array('class' => 'glyphicon glyphicon-move')));
		$td->add($link);
		return $td;
	}

}