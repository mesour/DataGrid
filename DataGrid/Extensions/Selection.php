<?php

namespace DataGrid\Extensions;

use DataGrid\Column;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Selection extends BaseControl {

	public $primary_key;

	public $url_array;

	public $show_main_checkbox;

	public function setPrimaryKey($primary_key) {
		$this->primary_key = $primary_key;
	}

	public function setUrlArray(array $url_array) {
		$this->url_array = $url_array;
	}

	public function setMainCheckboxShowing($show_main_checkbox) {
		$this->show_main_checkbox = $show_main_checkbox;
	}

	public function getSelectionColumn() {
		return new Column\Selection(array(
		    Column\Selection::ID => $this->primary_key,
		    Column\Selection::CHECKBOX_MAIN => $this->show_main_checkbox,
		    Column\Selection::CHECKBOX_ACTIONS => $this->url_array
		));
	}

	public function render() {
		$this->template->selections = $this->url_array;
		$this->template->grid_dir = __DIR__;

		$this->template->setFile(dirname(__FILE__) . '/templates/Selection.latte');
		$this->template->render();
	}

}