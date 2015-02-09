<?php

namespace Mesour\DataGrid\Column;

use Mesour\DataGrid\Grid_Exception;
use Nette\Application\UI\ITemplate;
use Nette\Utils\Callback;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Template extends BaseOrdering {

	/**
	 * Possible option key
	 */
	const TEMPLATE = 'template',
	    BLOCK = 'block',
	    CALLBACK = 'function',
	    CALLBACK_ARGS = 'func_args';

	public function setBlock($block) {
		$this->option[self::BLOCK] = $block;
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

	public function setTemplate($template) {
		if($template instanceof ITemplate || file_exists($template)) {
			$this->option[self::TEMPLATE] = $template;
		} else {
			throw new Grid_Exception('Template file ' . $template . ' does not exist.');
		}
		return $this;
	}

	protected function setDefaults() {
		return array_merge(parent::setDefaults(), array(
		    self::BLOCK => NULL
		));
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		if (array_key_exists(self::HEADER, $this->option) === FALSE) {
			throw new Grid_Exception('Option ' . __CLASS__ . '::HEADER is required.');
		}
		if (array_key_exists(self::TEMPLATE, $this->option) === FALSE) {
			throw new Grid_Exception('Option ' . __CLASS__ . '::TEMPLATE is required.');
		}
		return array(
		    'class' => 'grid-column-' . $this->option[self::ID]
		);
	}

	public function getHeaderContent() {
		return parent::getHeaderContent();
	}

	public function getBodyAttributes($data) {
		$attributes = array();
		$attributes['class'] = 'type-template';
		return parent::mergeAttributes($data, $attributes);
	}

	public function getBodyContent($data) {
		$template = $this->getTemplate();
		if (array_key_exists(self::CALLBACK, $this->option)) {
			Callback::check($this->option[self::CALLBACK]);
			$args = array($data, $template);
			if (isset($this->option[self::CALLBACK_ARGS]) && is_array($this->option[self::CALLBACK_ARGS])) {
				$args = array_merge($args, $this->option[self::CALLBACK_ARGS]);
			}
			Callback::invokeArgs($this->option[self::CALLBACK], $args);
		}
		return trim($template);
	}

	private function getTemplate() {
		if($this->option[self::TEMPLATE] instanceof ITemplate) {
			return $this->option[self::TEMPLATE];
		} else {
			$template = $this->grid->getCreatedTemplate();
			$template->setFile(__DIR__ . '/Template.latte');
			$template->_template_path = $this->option[self::TEMPLATE];
			$template->_block = FALSE;
			if(isset($this->option[self::BLOCK])) {
				$template->_block = $this->option[self::BLOCK];
			}
			return $template;
		}
	}

}