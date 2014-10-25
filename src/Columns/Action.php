<?php

namespace DataGrid\Column;

use \Nette\Utils\Html,
    \DataGrid\Grid_Exception,
    \DataGrid\Components\Link;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 * @deprecated
 */
class Action extends Base {

	/**
	 * Possible option key
	 */
	const ID = 'id',
	    LINK = 'link',
	    ACTIVE_BUTTON_CLASS = 'act_button_class',
	    USE_ITEM_ID = 'item_id';

	public function setId($id) {
		$this->option[self::ID] = $id;
		return $this;
	}

	public function setLink($link) {
		$this->option[self::LINK] = $link;
		return $this;
	}

	public function setAddedButtonClass($class) {
		$this->option[self::ACTIVE_BUTTON_CLASS] = $class;
		return $this;
	}

	public function setUseItemId($use = TRUE) {
		$this->option[self::USE_ITEM_ID] = $use;
		return $this;
	}

	private function checkPermissions() {
		$link = Link::checkLinkPermission($this->option[self::LINK]);
		if ($link === FALSE) {
			return FALSE;
		}
		return TRUE;
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		if (array_key_exists(self::LINK, $this->option) === FALSE) {
			throw new Grid_Exception('Option \DataGrid\ActionColumn::LINK is required.');
		}
		if (!$this->checkPermissions()) {
			return FALSE;
		}
		return array('class' => 'act buttons-count-1');
	}

	public function getHeaderContent() {
		return Html::el('b', array('class' => 'glyphicon glyphicon-ok-circle'));
	}

	public function getBodyAttributes($data) {
		if (!$this->checkPermissions()) {
			return FALSE;
		}
		if(!isset($data[self::$action_column_name])) {
			throw new Grid_Exception('Column "' . self::$action_column_name . '" does not exist in data. Can use \DataGrid\Column\Base::$action_column_name = "your_column_name" for change this column name.');
		}
		if ($data[self::$action_column_name] == self::$actions['active']) {
			return array('class' => 'is-unactive');
		} else {
			return array('class' => 'is-active');
		}
	}

	public function getBodyContent($data) {
		$added_class = isset($this->option[self::ACTIVE_BUTTON_CLASS]) ? ' ' . $this->option[self::ACTIVE_BUTTON_CLASS] : '';

		if (isset($this->option[self::USE_ITEM_ID]) && $this->option[self::USE_ITEM_ID]) {
			$params = array(
			    'id' => $this->grid->presenter->getParameter('id'),
			    'item_id' => $data[$this->option[self::ID]]
			);
		} else {
			$params = array(
			    'id' => $data[$this->option[self::ID]]
			);
		}

		if ($data[self::$action_column_name] == self::$actions['active']) {
			$params['status'] = self::$actions['unactive'];
			$link = Html::el('a', array(
			    'class' => 'ajax btn btn-sm btn-success' . $added_class,
			    'href' => $this->grid->presenter->link($this->option[self::LINK], $params),
			    'title' => 'Set as unactive (active)'
			));
			$link->add(Html::el('b', array('class' => 'glyphicon glyphicon-ok-circle')));
		} else {
			$params['status'] = self::$actions['active'];
			$link = Html::el('a', array(
			    'class' => 'ajax btn btn-sm btn-danger' . $added_class,
			    'href' => $this->grid->presenter->link($this->option[self::LINK], $params),
			    'title' => 'Set as active (unactive)'
			));
			$link->add(Html::el('b', array('class' => 'glyphicon glyphicon-ban-circle')));
		}
		return $link;
	}

}