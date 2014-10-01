<?php

namespace DataGrid\Column;

use \Nette\ComponentModel\IComponent,
	DataGrid\Grid_Exception;

/**
 * Description of \DataGrid\Column\Base
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
abstract class Base implements IColumn {

	/**
	 * Inner defaults
	 */
	public static $action_column_name = 'action';

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
	 * Option for this column
	 *
	 * @var Array
	 */
	protected $option = array();

	/**
	 * Data for current row
	 *
	 * @var mixed
	 */
	protected $data = array();

	/**
	 *
	 * @var \Nette\ComponentModel\IComponent
	 */
	protected $grid;

	/**
	 * @param array $option
	 */
	public function __construct(array $option = array()) {
		if(!empty($option)) {
			$this->option = $option;
		}
	}

	/**
	 * @param \Nette\ComponentModel\IComponent $grid
	 */
	public function setGridComponent(IComponent $grid) {
		$this->grid = $grid;
	}

	protected function getGrid() {
		return $this->grid;
	}

	public function createHeader() {
		$this->fixOption();
	}

	public function createBody($data) {
		if (empty($data)) {
			//throw new Grid_Exception('Empty data');
		}
		$this->data = $data;
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
			if (array_key_exists($key, $data)) {
				return $data[$key];
			} else {
				return $value;
			}
		} else {
			return $value;
		}
	}

	/**
	 * Check permissions for link
	 * 
	 * @param String $link
	 * @return String|FALSE
	 */
	static public function checkLinkPermission($link) {
		if (is_callable(self::$checkPermissionCallback)) {
			return call_user_func_array(self::$checkPermissionCallback, array($link));
		}
		return $link;
	}

	static public function getLink($link, array $arguments = array(), $data = NULL) {
		if (!empty($arguments)) {
			foreach ($arguments as $key => $value) {
				$params[$key] = self::parseValue($value, is_null($data) ? array() : $data);
			}
		} else {
			$params = array();
		}
		$to_href = self::checkLinkPermission($link);
		if ($to_href === FALSE) {
			return FALSE;
		}
		return array($to_href, $params);
	}

	/**
	 * Fix column option
	 *
	 * @throws Grid_Exception
	 */
	private function fixOption() {
		$isnt_special = (!$this instanceof Button && !$this instanceof Sortable && !$this instanceof Dropdown);
		if ($isnt_special && array_key_exists('id', $this->option) === FALSE) {
			throw new Grid_Exception('Column ID can not be empty.');
		}
		if ($isnt_special && array_key_exists('text', $this->option) === FALSE) {
			$this->option['text'] = $this->option['id'];
		}
	}

}