<?php

namespace Mesour\DataGrid;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
abstract class Setting {

	/** @var \Nette\Localization\ITranslator */
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
		if(!is_array($defaults)) {
			throw new Grid_Exception('Protected function setDefaults must return an array.');
		}
		$this->option = $defaults;
		if(!empty($option)) {
			$this->option = array_merge($defaults, $option);
		}
	}

	protected function setDefaults() {
		return array();
	}

	/**
	 * Sets translate adapter.
	 * @return self
	 */
	public function setTranslator(\Nette\Localization\ITranslator $translator)
	{
		$this->translator = $translator;
	}

	protected function getTranslator()
	{
		if($this->translator instanceof \Nette\Localization\ITranslator) {
			return $this->translator;
		} elseif( isset($this->grid) && $this->grid->getTranslator() instanceof \Nette\Localization\ITranslator) {
			return $this->grid->getTranslator();
		} else {
			return null;
		}
	}

}