<?php

namespace Mesour\DataGrid\Column;

use \Nette\Utils\Html,
    Mesour\DataGrid\Grid_Exception,
    Mesour\DataGrid\Components\DropDown,
    Mesour\DataGrid\Components\Button;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Actions extends Base {

	/**
	 * Possible option key
	 */
	const HEADER = 'header',
	    ACTIONS = 'actions';

	public function setHeader($header) {
		$this->option[self::HEADER] = $header;
		return $this;
	}

	public function addButton() {
		$button = new Button();
		$this->option[self::ACTIONS][] = $button;
		return $button;
	}

	public function addDropDown() {
		$button = new DropDown();
		$this->option[self::ACTIONS][] = $button;
		return $button;
	}

	protected function setDefaults() {
		return array(
			self::ACTIONS => array()
		);
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		if (array_key_exists(self::HEADER, $this->option) === FALSE) {
			throw new Grid_Exception('Option ' . __CLASS__ . '::HEADER is required.');
		}
		foreach ($this->option[self::ACTIONS] as $action) {
			$action->setPresenter($this->grid->presenter);
		}
		return array('class' => 'with-actions');
	}

	public function getHeaderContent() {
		return $this->getTranslator() ? $this->getTranslator()->translate($this->option[self::HEADER]) : $this->option[self::HEADER];
	}

	public function getBodyAttributes($data) {
		return parent::mergeAttributes($data, array('class' => 'button-component'));
	}

	public function getBodyContent($data) {
		$dropdown_count = 0;
		$container = Html::el('div');
		foreach ($this->option[self::ACTIONS] as $action) {
			if ($this->getTranslator()) {
				$action->setTranslator($this->getTranslator());
			}
			if($action instanceof DropDown) {
				$dropdown_count++;
			}
			$container->add($action->create($data) . ' ');
		}
		$container->class('buttons-count-' . (count($this->option[self::ACTIONS])-$dropdown_count) . ' dropdown-count-' . $dropdown_count);
		return $container;
	}

}