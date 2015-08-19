<?php

namespace Mesour\DataGrid\Column;

use Mesour\DataGrid\Grid_Exception;
use Nette\Utils\Callback;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Text extends InlineEdit {

	/**
	 * Possible option key
	 */
	const CALLBACK = 'function',
	    CALLBACK_ARGS = 'func_args';

    public function getTemplateFile() {
        return 'TextDropdown.latte';
    }

	public function setCallback($callback) {
		Callback::check($callback);
		$this->option[self::CALLBACK] = $callback;
		return $this;
	}

	public function setCallbackArguments(array $arguments) {
		$this->option[self::CALLBACK_ARGS] = $arguments;
		return $this;
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		if (array_key_exists(self::HEADER, $this->option) === FALSE) {
			throw new Grid_Exception('Option ' . __CLASS__ . '::HEADER is required.');
		}
		return array(
		    'class' => 'grid-column-' . $this->option[self::ID]
		);
	}

	public function getHeaderContent() {
		return parent::getHeaderContent();
	}

	public function getBodyAttributes($data) {
		$attributes = parent::getBodyAttributes($data);
		if (isset($this->grid['editable']) && $this->isEditable()) {
			$this->checkColumnId($data);
			$attributes = array_merge($attributes, array(
				'data-editable' => $this->option[self::ID],
				'data-editable-type' => 'text'
			));
		}
		$attributes['class'] = 'type-text';
		return parent::mergeAttributes($data, $attributes);
	}

	public function getBodyContent($data) {
		if (array_key_exists(self::CALLBACK, $this->option) === FALSE) {
			$this->checkColumnId($data);
			return $data[$this->option[self::ID]];
		} else {
			$args = array($data);
			if (isset($this->option[self::CALLBACK_ARGS]) && is_array($this->option[self::CALLBACK_ARGS])) {
				$args = array_merge($args, $this->option[self::CALLBACK_ARGS]);
			}
			return Callback::invokeArgs($this->option[self::CALLBACK], $args);
		}
	}

	private function checkColumnId($data) {
		if (isset($data[$this->option[self::ID]]) === FALSE && is_null($data[$this->option[self::ID]]) === FALSE) {
			throw new Grid_Exception('Column ' . $this->option[self::ID] . ' does not exists in DataSource.');
		}
	}

}