<?php

namespace Mesour\DataGrid\Column;

use Mesour\DataGrid\Grid_Exception,
	Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Container extends BaseOrdering {

	/**
	 * Possible option key
	 */
	const COLUMNS = 'columns';

	protected function setDefaults() {
		return array(
		    self::COLUMNS => array()
		);
	}

	public function addText($column_name, array $setting = array()) {
		$column = new Text($setting);
		$column->setId($column_name);
		return $this->option[self::COLUMNS][] = $column;
	}

	public function addDate($column_name, array $setting = array()) {
		$column = new Date($setting);
		$column->setId($column_name);
		return $this->option[self::COLUMNS][] = $column;
	}

	public function addNumber($column_name, array $setting = array()) {
		$column = new Number($setting);
		$column->setId($column_name);
		return $this->option[self::COLUMNS][] = $column;
	}

	public function addImage($column_name, array $setting = array()) {
		$column = new Image($setting);
		$column->setId($column_name);
		return $this->option[self::COLUMNS][] = $column;
	}

	public function addStatus($column_name, array $setting = array()) {
		$column = new Status($setting);
		$column->setId($column_name);
		return $this->option[self::COLUMNS][] = $column;
	}

	public function addButton(array $setting = array()) {
		$column = new Button($setting);
		return $this->option[self::COLUMNS][] = $column;
	}

	public function addDropdown(array $setting = array()) {
		$column = new Dropdown($setting);
		return $this->option[self::COLUMNS][] = $column;
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		foreach($this->option[self::COLUMNS] as $column) {
			$column->setGridComponent($this->grid);
		}
		if (array_key_exists(self::HEADER, $this->option) === FALSE) {
			throw new Grid_Exception('Option \Mesour\DataGrid\Column\Container::HEADER is required.');
		}
		if (array_key_exists(self::ID, $this->option) === FALSE) {
			$this->option[self::ORDERING] = FALSE;
		}
		return array(
		    'class' => 'grid-column-' . $this->option[self::ID]
		);
	}

	public function getHeaderContent() {
		return parent::getHeaderContent();
	}

	public function getBodyAttributes($data) {
		$attributes = array();
		$attributes['class'] = 'type-container';
		return $attributes;
	}

	public function getBodyContent($data) {
		$container = Html::el('span', array('class' => 'container-content'));
		$only_buttons = TRUE;
		foreach($this->option[self::COLUMNS] as $column) {
			if(!$column instanceof Button && !$column instanceof Dropdown && !$column instanceof Status) {
				$only_buttons = FALSE;
			}
			$span = Html::el('span');
			$span->addAttributes($column->getHeaderAttributes());
			$span->addAttributes($column->getBodyAttributes($data));
			$span->add($column->getBodyContent($data));
			$container->add($span);
			$container->add(' ');
		}
		if($only_buttons) {
			$container->class('only-buttons', TRUE);
		}
		return $container;
	}

}