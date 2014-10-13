<?php

namespace DataGrid;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class GridTree extends Grid {

	/**
	 * Render control
	 */
	public function render() {
		$this->template->grid_dir = __DIR__;

		$factory = new Render\Tree\RendererFactory($this);
		$table = $this->createBody($factory);
		$this->template->content = $table;

		$this->template->setFile(dirname(__FILE__) . '/templates/Grid.latte');
                $this->template->render();
	}

}