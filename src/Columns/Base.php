<?php

namespace DataGrid\Column;

use \Nette\ComponentModel\IComponent,
    DataGrid\Grid_Exception,
    DataGrid\Setting,
    \Nette\Localization\ITranslator;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
abstract class Base extends Setting implements IColumn {

	/**
	 * Inner defaults
	 * @deprecated
	 */
	public static $action_column_name = 'action';

	/**
	 * Actions setting
	 * @deprecated
	 *
	 * @var Array
	 */
	static public $actions = array(
	    'active' => 1,
	    'unactive' => 0
	);

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

	public function getId() {
		return isset($this->option['id']) ? $this->option['id'] : NULL;
	}

	public function getText() {
		if(isset($this->option['text']) && $this->getTranslator()) {
			return $this->getTranslator()->translate($this->option['text']);
		} elseif(isset($this->option['text'])) {
			return $this->option['text'];
		} else {
			return NULL;
		}
	}

	public function isEditable() {
		return isset($this->option['editable']) ? $this->option['editable'] : FALSE;
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		return array();
	}

	public function getBodyAttributes($data) {
		return array();
	}

	/**
	 * Fix column option
	 *
	 * @throws Grid_Exception
	 */
	protected function fixOption() {
		$isnt_special = (!$this instanceof Button && !$this instanceof Sortable && !$this instanceof Dropdown);
		if ($isnt_special && array_key_exists('id', $this->option) === FALSE) {
			throw new Grid_Exception('Column ID can not be empty.');
		}
		if ($isnt_special && array_key_exists('text', $this->option) === FALSE) {
			$this->option['text'] = $this->option['id'];
		}
	}

}