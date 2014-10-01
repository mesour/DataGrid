<?php

namespace DataGrid;

use \Nette\Utils\Html,
    \Nette\Application\UI\Presenter;

/**
 * Description of \DataGrid\SelectionColumn
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class SelectionColumn extends BaseColumn {
	/**
	 * Possible option key
	 */
	const ID = 'id',
	    CHECKBOX_ACTIONS = 'checkbox_acts',
	    CHECKBOX_MAIN = 'checkbox_main';

	/**
	 * @param \Nette\Application\UI\Presenter
	 * @param array $option
	 */
	public function __construct(Presenter $presenter, array $option = array()) {
		parent::__construct($presenter, $option);
	}

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

	/**
	 * Create HTML header
	 * 
	 * @return \Nette\Utils\Html
	 * @throws \DataGrid\Grid_Exception
	 */
	public function createHeader() {
		parent::createHeader();

		$th = Html::el('th', array('class' => 'act act-select'));
		$this->option[self::CHECKBOX_ACTIONS] = isset($this->option[self::CHECKBOX_ACTIONS]) ? $this->option[self::CHECKBOX_ACTIONS] : array();
		$one_active = FALSE;
		foreach ($this->option[self::CHECKBOX_ACTIONS] as $link) {
			if (self::checkLinkPermission($link) === FALSE) {
				//$one_unactive = TRUE;
			} else {
				$one_active = TRUE;
			}
		}
		if (!$one_active) {
			return '';
		}
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
			$ul->add(Html::el('li')->add(Html::el('a', array('href' => '#', 'data-select' => 'active'))->setText('Select active pages')));
			$ul->add(Html::el('li')->add(Html::el('a', array('href' => '#', 'data-select' => 'unactive'))->setText('Select unactive pages')));
			$ul->add(Html::el('li')->add(Html::el('a', array('href' => '#', 'data-select' => 'inverse'))->setText('Inverse selection')));
			$div->add($ul);
		} else {
			$button->class('btn btn-default btn-xs');
		}
		$div->add($button);
		$th->add($div);
		return $th;
	}

	/**
	 * Create HTML body
	 *
	 * @param $data
	 * @param string $container
	 * @return Html|string|void
	 */
	public function createBody($data, $container = 'th') {
		parent::createBody($data);

		$th = Html::el($container, array('class' => 'with-checkbox'));
		$one_active = FALSE;
		foreach ($this->option[self::CHECKBOX_ACTIONS] as $link) {
			if (self::checkLinkPermission($link) === FALSE) {
				//$one_unactive = TRUE;
			} else {
				$one_active = TRUE;
			}
		}
		if (!$one_active) {
			return '';
		}
		$checkbox = Html::el('a', array('data-value' => $this->data[$this->option[self::ID]], 'class' => 'btn btn-default btn-xs select-checkbox'));
		$checkbox->add('&nbsp;&nbsp;&nbsp;&nbsp;');
		$th->add($checkbox);
		return $th;
	}

}