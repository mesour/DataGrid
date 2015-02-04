<?php

namespace Mesour\DataGrid\Column;

use \Nette\Utils\Html,
    Nette\Utils\Callback,
    Mesour\DataGrid\Grid_Exception;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Image extends Base {

	/**
	 * Possible option key
	 */
	const ID = 'id',
	    HEADER = 'header',
	    MAX_WIDTH = 'max_width',
	    MAX_HEIGHT = 'max_height',
	    CALLBACK = 'function',
	    CALLBACK_ARGS = 'func_args';

	public function setId($id) {
		$this->option[self::ID] = $id;
		return $this;
	}

	public function setHeader($header) {
		$this->option[self::HEADER] = $header;
		return $this;
	}

	public function setMaxWidth($max_width) {
		$this->option[self::MAX_WIDTH] = $max_width;
		return $this;
	}

	public function setMaxHeight($max_height) {
		$this->option[self::MAX_HEIGHT] = $max_height;
		return $this;
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
		if (array_key_exists(self::CALLBACK, $this->option)) {
			Callback::check($this->option[self::CALLBACK]);
		}
		return $this->getTranslator() ? $this->getTranslator()->translate($this->option[self::HEADER]) : $this->option[self::HEADER];
	}

	public function getBodyContent($data) {
		if (array_key_exists(self::CALLBACK, $this->option) === FALSE) {
			if (isset($data[$this->option[self::ID]]) === FALSE && is_null($data[$this->option[self::ID]]) === FALSE) {
				throw new Grid_Exception('Column ' . $this->option[self::ID] . ' does not exists in DataSource.');
			}
			$src = $data[$this->option[self::ID]];
		} else {
			$args = array($data);
			if (isset($this->option[self::CALLBACK_ARGS])) {
				if(!is_array($this->option[self::CALLBACK_ARGS])) {
					throw new Grid_Exception(__CLASS__ . '::CALLBACK_ARGS must be an array. ' . gettype($this->option[self::CALLBACK_ARGS]) . ' given.');
				}
				$args = array_merge($args, $this->option[self::CALLBACK_ARGS]);
			}
			$src = Callback::invokeArgs($this->option[self::CALLBACK], $args);
		}

		$img = Html::el('img', array('src' => $src));
		if (isset($this->option[self::MAX_WIDTH]) === TRUE) {
			$img->style('max-width:' . $this->fixPixels($this->option[self::MAX_WIDTH]), TRUE);
		}
		if (isset($this->option[self::MAX_HEIGHT]) === TRUE) {
			$img->style('max-height:' . $this->fixPixels($this->option[self::MAX_HEIGHT]), TRUE);
		}
		return $img;
	}

	private function fixPixels($value) {
		return is_numeric($value) ? ($value . 'px') : $value;
	}

}