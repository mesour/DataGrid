<?php

namespace DataGrid\Column;

use \Nette\Utils\Html,
    \DataGrid\Components\Link;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Selection extends Base {

	/**
	 * Possible option key
	 */
	const ID = 'id',
	    CHECKBOX_ACTIONS = 'checkbox_acts',
	    CHECKBOX_MAIN = 'checkbox_main';

	public function setId($id) {
		$this->option[self::ID] = $id;
		return $this;
	}

	public function setActions(array $action_arr) {
		$this->option[self::CHECKBOX_ACTIONS] = $action_arr;
		return $this;
	}

	public function setShowMainCheckbox($main = TRUE) {
		$this->option[self::CHECKBOX_MAIN] = $main;
		return $this;
	}

	private function checkPermissions() {
		$this->option[self::CHECKBOX_ACTIONS] = isset($this->option[self::CHECKBOX_ACTIONS]) ? $this->option[self::CHECKBOX_ACTIONS] : array();
		$one_active = FALSE;
		foreach ($this->option[self::CHECKBOX_ACTIONS] as $link) {
			if (Link::checkLinkPermission($link) !== FALSE) {
				$one_active = TRUE;
			}
		}
		if (!$one_active) {
			return FALSE;
		}
		return TRUE;
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		if (!$this->checkPermissions()) {
			return FALSE;
		}
		return array('class' => 'act act-select');
	}

	public function getHeaderContent() {
		$div = Html::el('div', array('class' => 'btn-group', 'id' => 'checkbox-selector'));
		$button = Html::el('button', array('type' => 'button'));
		$checkbox = Html::el('a', array('type' => 'button', 'class' => 'btn btn-xs btn-default main-checkbox'));
		$checkbox->add('&nbsp;&nbsp;&nbsp;&nbsp;');
		$button->add($checkbox);

		$this->option[self::CHECKBOX_MAIN] = isset($this->option[self::CHECKBOX_MAIN]) ? $this->option[self::CHECKBOX_MAIN] : TRUE;

		if ($this->option[self::CHECKBOX_MAIN]) {
			$button->class('btn btn-default btn-xs dropdown-toggle');
			$button->add(' ');
			$button->add(Html::el('span', array('class' => 'caret')));

			$ul = Html::el('ul', array('class' => 'dropdown-menu', 'role' => 'menu'));
			//$ul->add(Html::el('li')->add(Html::el('a', array('href' => '#', 'data-select' => 'active'))->setText('Select active pages')));
			//$ul->add(Html::el('li')->add(Html::el('a', array('href' => '#', 'data-select' => 'unactive'))->setText('Select unactive pages')));
			$ul->add(Html::el('li')->add(Html::el('a', array('href' => '#', 'data-select' => 'inverse'))->setText($this->grid['translator']->translate('Inverse selection'))));
			$div->add($ul);
		} else {
			$button->class('btn btn-default btn-xs');
		}
		$div->add($button);
		return $div;
	}

	public function getBodyAttributes($data) {
		if (!$this->checkPermissions()) {
			return FALSE;
		}
		return array('class' => 'with-checkbox');
	}

	public function getBodyContent($data) {
		$checkbox = Html::el('a', array('data-value' => $data[$this->option[self::ID]], 'class' => 'btn btn-default btn-xs select-checkbox'));
		$checkbox->add('&nbsp;&nbsp;&nbsp;&nbsp;');
		return $checkbox;
	}

}