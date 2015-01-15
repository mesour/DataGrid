<?php

namespace Mesour\DataGrid\Extensions;

use Mesour\DataGrid\Column,
	Nette\ComponentModel\IContainer;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Selection extends BaseControl {

	public $primary_key;

	/**
	 * @var array
	 * @persistent
	 */
	public $selected = array();

	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		new SelectionLinks($this, 'links');
	}

	/**
	 * @return SelectionLinks
	 */
	public function getLinks() {
		return $this['links'];
	}

	public function setPrimaryKey($primary_key) {
		$this->primary_key = $primary_key;
	}

	public function getSelectionColumn() {
		return new Column\Selection(array(
		    Column\Selection::ID => $this->primary_key
		));
	}

	public function getTranslator() {
		return $this->parent->getTranslator();
	}

	public function render() {
		$this->template->links = $this['links']->getLinks();
		$this->template->grid_dir = __DIR__;

		$this->template->setFile(dirname(__FILE__) . '/Selection.latte');
		$this->template->render();
	}

	public function handleOnSelect($name) {
		$this['links']->getLink($name)->onCall($this->selected['items']);
		$this->parent->redrawControl();
		$this->presenter->redrawControl();
	}

}