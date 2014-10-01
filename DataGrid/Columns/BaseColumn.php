<?php

namespace DataGrid;

use \Nette\Application\UI\Presenter;

/**
 * Description of \DataGrid\BaseColumn
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
abstract class BaseColumn implements IColumn {

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
	 * @var \Nette\Application\UI\Presenter
	 */
	protected $presenter;

	/**
	 * @var String
	 */
	protected $grid_name;

	/**
	 * @param \Nette\Application\UI\Presenter $presenter
	 * @param array $option
	 */
	public function __construct(Presenter $presenter, array $option = array()) {
		$this->presenter = $presenter;
		if(!empty($option)) {
			$this->option = $option;
		}
	}

	public function setGridName($grid_name) {
		$this->grid_name = $grid_name;
	}

	protected function getGrid() {
		return $this->presenter[$this->grid_name];
	}

	public function createHeader() {
		$this->fixOption();
	}

	public function createBody($data) {
		if (empty($data)) {
			throw new Grid_Exception('Empty data');
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

	/**
	 * Fix column option
	 *
	 * @throws Grid_Exception
	 */
	private function fixOption() {
		if ((!$this instanceof ButtonColumn && !$this instanceof SortableColumn) && array_key_exists('id', $this->option) === FALSE) {
			throw new Grid_Exception('Column ID can not be empty.');
		}
		if ((!$this instanceof ButtonColumn && !$this instanceof SortableColumn) && array_key_exists('text', $this->option) === FALSE) {
			$this->option['text'] = $this->option['id'];
		}
	}

}