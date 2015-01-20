<?php

namespace Mesour\DataGrid\Components;

use \Nette\Application\UI\Presenter,
    Mesour\DataGrid\Setting,
    Mesour\DataGrid\Grid_Exception;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class StatusButton extends Button {

	/**
	 * Possible option key
	 */
	const STATUS = 'status',
	    CALLBACK = 'function',
	    CALLBACK_ARGS = 'func_args';

	/**
	 * Row data for button
	 *
	 * @var Array
	 */
	private $data = array();

	/**
	 *
	 * @var \Nette\Application\UI\Presenter
	 */
	protected $presenter;

	/**
	 * @param Presenter $presenter
	 * @param array $option
	 * @param null $data
	 */
	public function __construct(array $option = array(), Presenter $presenter = NULL, $data = NULL) {
		parent::__construct($option);
		if (empty($data) === FALSE) {
			$this->data = $data;
		}
		$this->presenter = $presenter;
	}

	public function setStatus($status) {
		$this->option[self::STATUS] = $status;
		return $this;
	}

	public function setCallback($callback) {
		$this->option[self::CALLBACK] = $callback;
		return $this;
	}

	public function setCallbackArguments(array $arguments) {
		$this->option[self::CALLBACK_ARGS] = $arguments;
		return $this;
	}

	public function getStatus() {
		return isset($this->option[self::STATUS]) ? $this->option[self::STATUS] : '';
	}

	public function isActive($column_name, $data) {
		$this->data = $data;
		if (array_key_exists(self::CALLBACK, $this->option) === FALSE) {
			return $this->data[$column_name] == $this->option[self::STATUS] ? TRUE : FALSE;
		} else {
			if (is_callable($this->option[self::CALLBACK])) {
				$args = array($data);
				if (isset($this->option[self::CALLBACK_ARGS]) && is_array($this->option[self::CALLBACK_ARGS])) {
					$args = array_merge($args, $this->option[self::CALLBACK_ARGS]);
				}
				return call_user_func_array($this->option[self::CALLBACK], $args);
			} else {
				throw new Grid_Exception('Callback in Component\StatusButton setting is not callable.');
			}
		}
	}

	/**
	 * Create button
	 *
	 * @param Array $data
	 * @return String
	 * @throws Grid_Exception
	 */
	public function create($data = NULL) {
		if (empty($data) === FALSE) {
			$this->data = $data;
		}
		if (is_null($this->presenter)) {
			throw new Grid_Exception('Presenter is not set for ' . __CLASS__ . '.');
		}
		if (!isset($this->option[self::STATUS]) && !isset($this->option[self::CALLBACK])) {
			throw new Grid_Exception('Option ' . __CLASS__ . '::STATUS is required.');
		}
		return parent::create($data);
	}

	/**
	 * See method create
	 *
	 * @return String
	 */
	public function __toString() {
		return $this->create();
	}

}