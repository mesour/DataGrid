<?php

namespace DataGrid\Column;

use \Nette\Utils\Html,
    \DataGrid\Grid_Exception;

/**
 * Description of \DataGrid\Column\Text
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
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

	/**
	 * Create HTML header
	 *
	 * @return \Nette\Utils\Html
	 * @throws \DataGrid\Grid_Exception
	 */
	public function createHeader() {
		parent::createHeader();
		$th = Html::el('th');
		if (array_key_exists(self::TEXT, $this->option) === FALSE) {
			throw new Grid_Exception('Option \DataGrid\TextColumn::TEXT is required.');
		}
		$this->addHeaderOrdering($th);
		return $th;
	}

	/**
	 * Create HTML body
	 *
	 * @param mixed $data
	 * @param string $container
	 * @return Html|void
	 * @throws Grid_Exception
	 */
	public function createBody($data, $container = 'td') {
		parent::createBody($data);

		$span = Html::el($container);
		if ($this->grid->isEditable() && $this->option[self::EDITABLE]) {
			$this->checkColumnId();
			$span->addAttributes(array('data-editable' => $this->option[self::ID]));
		}
		if (array_key_exists(self::CALLBACK, $this->option) === FALSE) {
			$this->checkColumnId();
			$span->setHtml($this->data[$this->option[self::ID]]);
		} else {
			if (is_callable($this->option[self::CALLBACK])) {
				$args = array($this->data);
				if (isset($this->option[self::CALLBACK_ARGS]) && is_array($this->option[self::CALLBACK_ARGS])) {
					$args = array_merge($args, $this->option[self::CALLBACK_ARGS]);
				}
				$span->setHtml(call_user_func_array($this->option[self::CALLBACK], $args));
			} else {
				throw new Grid_Exception('Callback in column options is not callable.');
			}
		}
		return $span;
	}

	private function checkColumnId() {
		if (isset($this->data[$this->option[self::ID]]) === FALSE && is_null($this->data[$this->option[self::ID]]) === FALSE) {
			throw new Grid_Exception('Column ' . $this->option[self::ID] . ' does not exists in DataSource.');
		}
	}

}