<?php

namespace DataGrid;

use \Nette\Utils\Html;

/**
 * Description of \DataGrid\Column
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class Column {
	/**
	 * Possible type
	 */

	const TEXT = 'text',
		BUTTONS = 'buttons',
		ACTIVE_BUTTON = 'active_button',
		CHECKBOX_SELECTION = 'checkbox',
		SORT = 'sort';

	/**
	 * Possible option key
	 */
	const _ID = 'id',
		_TEXT = 'text',
		_TYPE = 'type',
		_BUTTONS_OPTION = 'buttons_option',
		_LINK = 'link',
		_FUNCTION = 'function',
		_ORDERING = 'ordering';

    /**
     * Only for type = self::_FUNCTION
     */
    const _FUNCTION_ARGS = 'function_args';

	/**
	 * Only for self::ACTIVE_BUTTON
	 */
	const _ACTIVE_BUTTON_CLASS = 'act_button_class',
		_USE_ITEM_ID = 'item_id';

	/**
	 * Only for self::CHECKBOX_SELECTION
	 */
	const _CHECKBOX_ACTIONS = 'checkbox_acts',
		_CHECKBOX_MAIN = 'checkbox_main';

	/**
	 * Inner defaults
	 */
	const __ACTION_COLUMN_NAME = 'action';

	/**
	 * Actions setting
	 *
	 * @var Array
	 */
	static public $actions = array(
	    'active' => 1,
	    'unactive' => 0
	);

	/**
	 * Valid permission callback
	 *
	 * @var Mixed
	 */
	static public $checkPermissionCallback;

	/**
	 * Option for this row
	 *
	 * @var Array
	 */
	private $option = array();

	/**
	 * Data for row
	 *
	 * @var Array
	 */
	private $data = array();

	/**
	 *
	 * @var \Nette\Application\Presenter
	 */
	private $presenter;

	/**
	 * Create instance
	 * 
	 * @param Array $option
	 * @param Array $data
	 * @throws \DataGrid\Grid_Exception
	 */
	public function __construct(array $option, $data = NULL) {
		if ((isset($option[self::_TYPE]) && $option[self::_TYPE] !== self::BUTTONS && $option[self::_TYPE] !== self::SORT
			) && array_key_exists(self::_ID, $option) === FALSE)
			throw new \DataGrid\Grid_Exception('Column ID can not be empty.');
		if (empty($data) === FALSE)
			$this->data = $data;
		$this->option = $option;
		$this->presenter = \Nette\Environment::getApplication()->getPresenter();
		$this->fixOption();
	}

	public function getType() {
		return $this->option[self::_TYPE];
	}

	/**
	 * Parse value with {identifier}
	 * 
	 * @param String $value
	 * @param Array $data
	 * @return Array
	 */
	static public function parseValue($value, $data) {
		if (substr($value, 0, 1) === '{' && substr($value, -1) === '}') {
			$key = substr($value, 1, strlen($value) - 2);
			if (array_key_exists($key, $data))
				return $data[$key];
			else
				return $value;
		} else {
			return $value;
		}
	}

	/**
	 * Check permissions for link
	 * 
	 * @param String $link
	 * @return bool
	 */
	static public function checkLinkPermission($link) {
		if (is_callable(self::$checkPermissionCallback)) {
			return call_user_func_array(self::$checkPermissionCallback, array($link));
		}
		return $link;
	}

	/**
	 * Create HTML header
	 * 
	 * @return \Nette\Utils\Html
	 * @throws \DataGrid\Grid_Exception
	 */
	public function createHeader() {
		$th = Html::el('th');
		switch ($this->option[self::_TYPE]) {
			case self::TEXT:
				if (isset($this->option[self::_ORDERING]) && $this->option[self::_ORDERING]) {
					$link = Html::el('a', array('href' => '#'));
					$link->setText($this->option[self::_TEXT]);
					$th->add($link);
				} else {
					$th->setText($this->option[self::_TEXT]);
				}
				break;
			case self::ACTIVE_BUTTON:
				$link = self::checkLinkPermission($this->option[self::_LINK]);
				if ($link === FALSE) {
					return '';
				}
				$th->class('act buttons-count-1');
				$th->add(Html::el('b', array('class' => 'glyphicon glyphicon-ok-circle')));
				break;
			case self::CHECKBOX_SELECTION:
				$this->option[self::_CHECKBOX_ACTIONS] = isset($this->option[self::_CHECKBOX_ACTIONS]) ? $this->option[self::_CHECKBOX_ACTIONS] : array();
				$one_active = FALSE;
				foreach ($this->option[self::_CHECKBOX_ACTIONS] as $link) {
					if (self::checkLinkPermission($link) === FALSE) {
						//$one_unactive = TRUE;
					} else {
						$one_active = TRUE;
					}
				}
				if (!$one_active) {
					return '';
				}
				$th->class('act act-select');
				$div = Html::el('div', array('class' => 'btn-group', 'id' => 'checkbox-selector'));
				$button = Html::el('button', array('type' => 'button'));
				$checkbox = Html::el('a', array('type' => 'button', 'class' => 'btn btn-xs btn-default main-checkbox'));
				$checkbox->add('&nbsp;&nbsp;&nbsp;&nbsp;');
				$button->add($checkbox);

				$this->option[self::_CHECKBOX_MAIN] = isset($this->option[self::_CHECKBOX_MAIN]) ? $this->option[self::_CHECKBOX_MAIN] : TRUE;

				if ($this->option[self::_CHECKBOX_MAIN]) {
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
				break;
			case self::SORT:
				$th->class('act buttons-count-1');
				$th->add(Html::el('b', array('class' => 'glyphicon glyphicon-move')));
				break;
			case self::BUTTONS:
				if (array_key_exists(self::_BUTTONS_OPTION, $this->option) === FALSE) {
					throw new \DataGrid\Grid_Exception('Key ' . self::_BUTTONS_OPTION . ' does not exists in column option.');
				}
				if (is_array($this->option[self::_BUTTONS_OPTION]) === FALSE) {
					throw new \DataGrid\Grid_Exception('Key ' . self::_BUTTONS_OPTION . ' must be an array.');
				}
				$count = count($this->option[self::_BUTTONS_OPTION]);
				$th->class('act buttons-count-' . $count);
				$th->setText($this->option[self::_TEXT]);
				break;
		}
		return $th;
	}

	/**
	 * Create HTML body
	 * 
	 * @param Array $data
	 * @return \Nette\Utils\Html
	 * @throws \DataGrid\Grid_Exception
	 */
	public function createBody($data = NULL, $container = 'span') {
		if (empty($data) === FALSE)
			$this->data = $data;
		if (empty($this->data))
			throw new \DataGrid\Grid_Exception('Empty data');
		$span = Html::el($container);
		if ((isset($this->option[self::_TYPE]) && ( $this->option[self::_TYPE] !== self::BUTTONS && $this->option[self::_TYPE] !== self::SORT )
			) && array_key_exists(self::_TEXT, $this->option) === FALSE)
			throw new \DataGrid\Grid_Exception('Column ' . $this->option[self::_ID] . ' does not exists in data source.');
		switch ($this->option[self::_TYPE]) {
			case self::TEXT:
				if (array_key_exists(self::_FUNCTION, $this->option) === FALSE) {
					if (isset($this->data[$this->option[self::_ID]]) === FALSE && is_null($this->data[$this->option[self::_ID]]) === FALSE)
						throw new \DataGrid\Grid_Exception('Column ' . $this->option[self::_ID] . ' does not exists in DataSource.');
					$span->setHtml($this->data[$this->option[self::_ID]]);
				} else {
                    if (is_callable($this->option[self::_FUNCTION])) {
                        $args = array($this->data);
                        if(isset($this->option[self::_FUNCTION_ARGS]) && is_array($this->option[self::_FUNCTION_ARGS])) {
                            $args = array_merge($args, $this->option[self::_FUNCTION_ARGS]);
                        }
                        $span->setHtml(call_user_func_array($this->option[self::_FUNCTION], $args));
                    } else {
						throw new \DataGrid\Grid_Exception('Function in column options is not callable.');
					}
				}
				break;
			case self::ACTIVE_BUTTON:
				if (array_key_exists(self::_LINK, $this->option) === FALSE)
					throw new \DataGrid\Grid_Exception('Key ' . self::_LINK . ' does not exists in column option.');
				$to_href = self::checkLinkPermission($this->option[self::_LINK]);
				$added_class = isset($this->option[self::_ACTIVE_BUTTON_CLASS]) ? ' ' . $this->option[self::_ACTIVE_BUTTON_CLASS] : '';
				if ($to_href === FALSE)
					return '';

				if (isset($this->option[self::_USE_ITEM_ID]) && $this->option[self::_USE_ITEM_ID]) {
					$params = array(
					    'id' => $this->presenter->getParam('id'),
					    'item_id' => $this->data[$this->option[self::_ID]]
					);
				} else {
					$params = array(
					    'id' => $this->data[$this->option[self::_ID]]
					);
				}

				if ($this->data[self::__ACTION_COLUMN_NAME] == self::$actions['active']) {
					$params['status'] = self::$actions['unactive'];
					$link = Html::el('a', array(
						    'class' => 'ajax btn btn-sm btn-success' . $added_class,
						    'href' => $this->presenter->link($to_href, $params),
						    'title' => 'Set as unactive (active)'
						));
					$link->add(Html::el('b', array('class' => 'glyphicon glyphicon-ok-circle')));
					$span->class('is-active');
					$span->add($link);
				} else {
					$params['status'] = self::$actions['active'];
					$link = Html::el('a', array(
						    'class' => 'ajax btn btn-sm btn-danger' . $added_class,
						    'href' => $this->presenter->link($to_href, $params),
						    'title' => 'Set as active (unactive)'
						));
					$span->class('is-unactive');
					$link->add(Html::el('b', array('class' => 'glyphicon glyphicon-ban-circle')));
					$span->add($link);
				}
				break;
			case self::CHECKBOX_SELECTION:
				$one_active = FALSE;
				foreach ($this->option[self::_CHECKBOX_ACTIONS] as $link) {
					if (self::checkLinkPermission($link) === FALSE) {
						//$one_unactive = TRUE;
					} else {
						$one_active = TRUE;
					}
				}
				if (!$one_active) {
					return '';
				}
				$checkbox = Html::el('a', array('data-value' => $this->data[$this->option[self::_ID]], 'class' => 'btn btn-default btn-xs select-checkbox'));
				$checkbox->add('&nbsp;&nbsp;&nbsp;&nbsp;');
				$span->class('with-checkbox');
				$span->add($checkbox);
				break;
			case self::BUTTONS:
				$count = count($this->option[self::_BUTTONS_OPTION]);
				$container = Html::el('div', array('class' => 'thumbnailx buttons-count-' . $count));

				foreach ($this->option[self::_BUTTONS_OPTION] as $button) {
					$button = new \DataGrid\Button($button, $this->data);
					$container->add($button->create() . ' ');
				}
				$span->class('right-buttons');
				$span->add($container);
				break;
			case self::SORT:
				$link = Html::el('a', array('class' => 'btn btn-default move handler', 'href' => '#'));
				$link->add(Html::el('b', array('class' => 'glyphicon glyphicon-move')));
				$span->add($link);
				break;
		}
		return $span;
	}

	/**
	 * See create body
	 * 
	 * @return String
	 */
	public function __toString() {
		return $this->createBody()->render();
	}

	/**
	 * Fix column option
	 */
	private function fixOption() {
		if (array_key_exists(self::_TYPE, $this->option) === FALSE)
			$this->option[self::_TYPE] = self::TEXT;
		if ((isset($this->option[self::_TYPE]) && ( $this->option[self::_TYPE] !== self::BUTTONS && $this->option[self::_TYPE] !== self::SORT )
			) && array_key_exists(self::_TEXT, $this->option) === FALSE)
			$this->option[self::_TEXT] = $this->option[self::_ID];
	}

}