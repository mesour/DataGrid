<?php

namespace Mesour\DataGrid;

use Nette\Localization\ITranslator;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
abstract class Setting {

	/**
	 * @var \Nette\Localization\ITranslator
	 */
	protected $translator;

	/**
	 * Option for this column
	 *
	 * @var Array
	 */
	protected $option = array();

	/**
	 * @param array $option
	 * @throws Grid_Exception
	 */
	public function __construct(array $option = array()) {
		$defaults = $this->setDefaults();
		if (!is_array($defaults)) {
			throw new Grid_Exception('Method setDefaults must return an array.');
		}
		$this->option = $defaults;
		if (!empty($option)) {
			$this->option = array_merge($defaults, $option);
		}
	}

	protected function setDefaults() {
		return array();
	}

	/**
	 * Sets translate adapter.
	 *
	 * @param ITranslator $translator
	 */
	public function setTranslator(ITranslator $translator) {
		$this->translator = $translator;
	}

	protected function getTranslator() {
		if ($this->translator instanceof ITranslator) {
			return $this->translator;
		} elseif (isset($this->grid) && $this->grid->getTranslator() instanceof ITranslator) {
			return $this->grid->getTranslator();
		} else {
			return null;
		}
	}

}