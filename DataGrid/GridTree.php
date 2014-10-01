<?php

namespace DataGrid;

/**
 * Description of \DataGrid\GridTree
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class GridTree extends Grid {
	
	/**
	 * Render control
	 */
	public function render() {
		$this->template->filter_form = $this->filter_form;
		$this->template->selections = $this->selections;
		$this->template->sortable = $this->sortable;
		$this->template->grid_dir = __DIR__;

		$this->template->setFile( dirname( __FILE__ ) . '/templates/DataGridTree.latte' );
                $this->template->render();
	}
	
}