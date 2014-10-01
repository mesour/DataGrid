<?php

namespace DataGrid\Column;

use \Nette\Utils\Html,
    \DataGrid\Grid_Exception;

/**
 * Description of \DataGrid\Columns\Action
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
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

	/**
	 * Create HTML header
	 * 
	 * @return \Nette\Utils\Html
	 * @throws \DataGrid\Grid_Exception
	 */
	public function createHeader() {
		parent::createHeader();

		if (array_key_exists(self::LINK, $this->option) === FALSE) {
			throw new Grid_Exception('Option \DataGrid\ActionColumn::LINK is required.');
		}

		$th = Html::el('th', array('class' => 'act buttons-count-1'));
		$link = self::checkLinkPermission($this->option[self::LINK]);
		if ($link === FALSE) {
			return '';
		}
		$th->add(Html::el('b', array('class' => 'glyphicon glyphicon-ok-circle')));
		return $th;
	}

	/**
	 * Create HTML body
	 *
	 * @param mixed $data
	 * @param String $container
	 * @return Html|string|void
	 */
	public function createBody($data, $container = 'td') {
		parent::createBody($data);

		$td = Html::el($container);

		$to_href = self::checkLinkPermission($this->option[self::LINK]);
		$added_class = isset($this->option[self::ACTIVE_BUTTON_CLASS]) ? ' ' . $this->option[self::ACTIVE_BUTTON_CLASS] : '';
		if ($to_href === FALSE) {
			return '';
		}

		if (isset($this->option[self::USE_ITEM_ID]) && $this->option[self::USE_ITEM_ID]) {
			$params = array(
			    'id' => $this->grid->presenter->getParameter('id'),
			    'item_id' => $this->data[$this->option[self::ID]]
			);
		} else {
			$params = array(
			    'id' => $this->data[$this->option[self::ID]]
			);
		}

		if ($this->data[self::$action_column_name] == self::$actions['active']) {
			$params['status'] = self::$actions['unactive'];
			$link = Html::el('a', array(
			    'class' => 'ajax btn btn-sm btn-success' . $added_class,
			    'href' => $this->grid->presenter->link($to_href, $params),
			    'title' => 'Set as unactive (active)'
			));
			$link->add(Html::el('b', array('class' => 'glyphicon glyphicon-ok-circle')));
			$td->class('is-active');
			$td->add($link);
		} else {
			$params['status'] = self::$actions['active'];
			$link = Html::el('a', array(
			    'class' => 'ajax btn btn-sm btn-danger' . $added_class,
			    'href' => $this->grid->presenter->link($to_href, $params),
			    'title' => 'Set as active (unactive)'
			));
			$td->class('is-unactive');
			$link->add(Html::el('b', array('class' => 'glyphicon glyphicon-ban-circle')));
			$td->add($link);
		}
		return $td;
	}

}