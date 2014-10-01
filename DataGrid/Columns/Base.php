<?php

namespace DataGrid\Column;

use \Nette\ComponentModel\IComponent,
	DataGrid\Grid_Exception,
    DataGrid\Utils\Option;

/**
 * Description of \DataGrid\Column\Base
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
abstract class Base extends Option implements IColumn {

	/**
	 * Inner defaults
	 */
	public static $action_column_name = 'action';

	/**
	 * Actions setting
	 *
	 * @var Array
	 */
	static public $actions = array(
	    'active' => 1,
	    'unactive' => 0
	);

	/**
	 * Data for current row
	 *
	 * @var mixed
	 */
	protected $data = array();

	/**
	 *
	 * @var \DataGrid\Grid
	 */
	protected $grid;

	/**
	 * @param \Nette\ComponentModel\IComponent $grid
	 */
	public function setGridComponent(IComponent $grid) {
		$this->grid = $grid;
	}

	protected function getGrid() {
		return $this->grid;
	}

	public function createHeader() {
		$this->fixOption();
	}

	public function createBody($data) {
		if (empty($data)) {
			//throw new Grid_Exception('Empty data');
		}
		$this->data = $data;
	}

	/**
	 * Fix column option
	 *
	 * @throws Grid_Exception
	 */
	private function fixOption() {
		$isnt_special = (!$this instanceof Button && !$this instanceof Sortable && !$this instanceof Dropdown);
		if ($isnt_special && array_key_exists('id', $this->option) === FALSE) {
			throw new Grid_Exception('Column ID can not be empty.');
		}
		if ($isnt_special && array_key_exists('text', $this->option) === FALSE) {
			$this->option['text'] = $this->option['id'];
		}
	}

}