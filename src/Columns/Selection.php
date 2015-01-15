<?php

namespace Mesour\DataGrid\Column;

use \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Selection extends Base {

	/**
	 * Possible option key
	 */
	const ID = 'id';

	public function setId($id) {
		$this->option[self::ID] = $id;
		return $this;
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		return array('class' => 'act act-select');
	}

	public function getHeaderContent() {
		$div = Html::el('div', array('class' => 'btn-group', 'id' => 'checkbox-selector'));

		$button = Html::el('button', array('type' => 'button'));
		$checkbox = Html::el('a', array('type' => 'button', 'class' => 'btn btn-xs btn-default main-checkbox'));
		$checkbox->add('&nbsp;&nbsp;&nbsp;&nbsp;');
		$button->add($checkbox);

		$button->class('btn btn-default btn-xs dropdown-toggle');
		$button->add(' ');
		$button->add(Html::el('span', array('class' => 'caret')));

		$ul = Html::el('ul', array('class' => 'dropdown-menu', 'role' => 'menu'));
		//$ul->add(Html::el('li')->add(Html::el('a', array('href' => '#', 'data-select' => 'active'))->setText('Select active pages')));
		//$ul->add(Html::el('li')->add(Html::el('a', array('href' => '#', 'data-select' => 'unactive'))->setText('Select unactive pages')));
		$ul->add(Html::el('li')->add(Html::el('a', array('href' => '#', 'data-select' => 'inverse'))->setText($this->grid['translator']->translate('Inverse selection'))));
		$div->add($ul);

		$div->add($button);

		return $div;
	}

	public function getBodyAttributes($data) {
		return parent::mergeAttributes($data, array('class' => 'with-checkbox'));
	}

	public function getBodyContent($data) {
		$checkbox = Html::el('a', array('data-value' => $data[$this->option[self::ID]], 'class' => 'btn btn-default btn-xs select-checkbox'));
		$checkbox->add('&nbsp;&nbsp;&nbsp;&nbsp;');
		return $checkbox;
	}

}