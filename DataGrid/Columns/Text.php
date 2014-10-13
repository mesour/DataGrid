<?php

namespace DataGrid\Column;

use \DataGrid\Grid_Exception;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Text extends BaseOrdering {

	/**
	 * Possible option key
	 */
	const EDITABLE = 'editable',
	    CALLBACK = 'function',
	    CALLBACK_ARGS = 'func_args';

	public function setCallback(callable $callback) {
		$this->option[self::CALLBACK] = $callback;
		return $this;
	}

	public function setCallbackArguments(array $arguments) {
		$this->option[self::CALLBACK_ARGS] = $arguments;
		return $this;
	}

	public function setEditable($editable) {
		$this->option[self::EDITABLE] = (bool)$editable;
		return $this;
	}

	protected function setDefaults() {
		return array(
		    self::EDITABLE => TRUE
		);
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		if (array_key_exists(self::TEXT, $this->option) === FALSE) {
			throw new Grid_Exception('Option \DataGrid\TextColumn::TEXT is required.');
		}
		return array(
		    'class' => 'grid-column-' . $this->option[self::ID]
		);
	}

	public function getHeaderContent() {
		return parent::getHeaderContent();
	}

	public function getBodyAttributes($data) {
		if (isset($this->grid['editable']) && $this->option[self::EDITABLE]) {
			$this->checkColumnId($data);
			return array(
			    'data-editable' => $this->option[self::ID],
			    'data-editable-type' => 'text'
			);
		}
		return array();
	}

	public function getBodyContent($data) {
		if (array_key_exists(self::CALLBACK, $this->option) === FALSE) {
			$this->checkColumnId($data);
			return $data[$this->option[self::ID]];
		} else {
			if (is_callable($this->option[self::CALLBACK])) {
				$args = array($data);
				if (isset($this->option[self::CALLBACK_ARGS]) && is_array($this->option[self::CALLBACK_ARGS])) {
					$args = array_merge($args, $this->option[self::CALLBACK_ARGS]);
				}
				return call_user_func_array($this->option[self::CALLBACK], $args);
			} else {
				throw new Grid_Exception('Callback in column setting is not callable.');
			}
		}
	}

	private function checkColumnId($data) {
		if (isset($data[$this->option[self::ID]]) === FALSE && is_null($data[$this->option[self::ID]]) === FALSE) {
			throw new Grid_Exception('Column ' . $this->option[self::ID] . ' does not exists in DataSource.');
		}
	}

}