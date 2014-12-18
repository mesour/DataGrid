<?php

namespace Mesour\DataGrid\Components;

use \Nette\Application\UI\Presenter,
    Mesour\DataGrid\Setting,
    Mesour\DataGrid\Grid_Exception;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class ButtonsContainer extends Setting {

	/**
	 * Possible option key
	 */
	const BUTTONS = 'buttons';

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
	public function __construct(Presenter $presenter = NULL, array $option = array(), $data = NULL) {
		parent::__construct($option);
		if (empty($data) === FALSE) {
			$this->data = $data;
		}
		$this->presenter = $presenter;
	}

	public function setPresenter(Presenter $presenter) {
		$this->presenter = $presenter;
	}

	public function setButtons(array $buttons) {
		$this->option[self::BUTTONS] = $buttons;
		return $this;
	}

	public function addButton(Button $button) {
		$this->option[self::BUTTONS][] = $button;
		return $this;
	}

	public function getButtonsCount() {
		return count($this->option[self::BUTTONS]);
	}

	protected function setDefaults() {
		return array(
		    self::BUTTONS => array(),
		);
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
			throw new Grid_Exception('Presenter is not set for Button.');
		}
		$buttons = '';
		foreach ($this->option[self::BUTTONS] as $button) {
			if (!$button instanceof Button) {
				throw new Grid_Exception('Button must be instanceof Components\Button.');
			}
			$button->setPresenter($this->presenter);
			if ($this->getTranslator()) {
				$button->setTranslator($this->getTranslator());
			}
			$buttons .= $button->create($data) . ' ';
		}
		return $buttons;
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