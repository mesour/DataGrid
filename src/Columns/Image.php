<?php

namespace DataGrid\Column;

use \Nette\Utils\Html,
    \DataGrid\Grid_Exception;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Image extends Base {

	/**
	 * Possible option key
	 */
	const ID = 'id',
	    TEXT = 'text',
	    MAX_WIDTH = 'max_width',
	    MAX_HEIGHT = 'max_height',
	    CALLBACK = 'function',
	    CALLBACK_ARGS = 'func_args';

	public function setId($id) {
		$this->option[self::ID] = $id;
		return $this;
	}

	public function setText($text) {
		$this->option[self::TEXT] = $text;
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
		$this->option[self::CALLBACK] = $callback;
		return $this;
	}

	public function setCallbackArguments(array $arguments) {
		$this->option[self::CALLBACK_ARGS] = $arguments;
		return $this;
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		if (array_key_exists(self::TEXT, $this->option) === FALSE) {
			throw new Grid_Exception('Option \DataGrid\ImageColumn::TEXT is required.');
		}
		return array(
		    'class' => 'grid-column-' . $this->option[self::ID]
		);
	}

	public function getHeaderContent() {
		return $this->getTranslator() ? $this->getTranslator()->translate($this->option[self::TEXT]) : $this->option[self::TEXT];
	}

	public function getBodyContent($data) {
		if (array_key_exists(self::CALLBACK, $this->option) === FALSE) {
			if (isset($data[$this->option[self::ID]]) === FALSE && is_null($data[$this->option[self::ID]]) === FALSE) {
				throw new Grid_Exception('Column ' . $this->option[self::ID] . ' does not exists in DataSource.');
			}
			$src = $data[$this->option[self::ID]];
		} else {
			if (is_callable($this->option[self::CALLBACK])) {
				$args = array($data);
				if (isset($this->option[self::CALLBACK_ARGS]) && is_array($this->option[self::CALLBACK_ARGS])) {
					$args = array_merge($args, $this->option[self::CALLBACK_ARGS]);
				}
				$src = call_user_func_array($this->option[self::CALLBACK], $args);
			} else {
				throw new Grid_Exception('Callback in column setting is not callable.');
			}
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