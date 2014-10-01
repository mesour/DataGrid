<?php

namespace DataGrid;

use \Nette\Utils\Html,
    \Nette\Application\UI\Presenter;

/**
 * Description of \DataGrid\TextColumn
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class TextColumn extends BaseOrderingColumn {

	/**
	 * Possible option key
	 */
	const CALLBACK = 'function',
	    CALLBACK_ARGS = 'func_args';

	/**
	 * @param \Nette\Application\UI\Presenter
	 * @param array $option
	 */
	public function __construct(Presenter $presenter, array $option = array()) {
		parent::__construct($presenter, $option);
	}

	public function setCallback(callable $callback) {
		$this->option[self::CALLBACK] = $callback;
		return $this;
	}

	public function setCallbackArguments(array $arguments) {
		$this->option[self::CALLBACK_ARGS] = $arguments;
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
		if (array_key_exists(self::CALLBACK, $this->option) === FALSE) {
			if (isset($this->data[$this->option[self::ID]]) === FALSE) {
				throw new Grid_Exception('Column ' . $this->option[self::ID] . ' does not exists in DataSource.');
			}
			$span->setHtml($this->data[$this->option[self::ID]]);
		} else {
			if (is_callable($this->option[self::CALLBACK])) {
				$args = array($this->data);
				if(isset($this->option[self::CALLBACK_ARGS]) && is_array($this->option[self::CALLBACK_ARGS])) {
					$args = array_merge($args, $this->option[self::CALLBACK_ARGS]);
				}
				$span->setHtml(call_user_func_array($this->option[self::CALLBACK], $args));
			} else {
				throw new Grid_Exception('Callback in column options is not callable.');
			}
		}
		return $span;
	}

}