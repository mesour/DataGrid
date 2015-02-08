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

	/**
	 * @var SelectionLinks
	 */
	private $links;

	private $enabled = FALSE;

	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		$this->links = new SelectionLinks($this);
	}

	/**
	 * @return SelectionLinks
	 */
	public function getLinks() {
		return $this->links;
	}

	public function enable() {
		$this->enabled = TRUE;
	}

	public function isEnabled() {
		return $this->enabled;
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
		$this->template->links = $this->links->getLinks();
		$this->template->grid_dir = __DIR__;

		$this->template->setFile(dirname(__FILE__) . '/Selection.latte');
		$this->template->render();
	}

	public function handleOnSelect($name) {
		$this->getLinks()->getLink($name)
		    ->onCall($this->selected['items']);
		$this->parent->redrawControl();
		$this->presenter->redrawControl();
	}

}