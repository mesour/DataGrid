<?php

namespace Mesour\DataGrid\Column;

use \Nette\Utils\Html,
    Mesour\DataGrid\Grid_Exception,
    Mesour\DataGrid\Components;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Button extends Base {

	/**
	 * Possible option key
	 */
	const HEADER = 'header',
	    BUTTONS_OPTION = 'buttons_option';

	public function setHeader($header) {
		$this->option[self::HEADER] = $header;
		return $this;
	}

	public function setButtons(Components\ButtonsContainer $container) {
		$this->option[self::BUTTONS_OPTION] = $container;
		return $this;
	}

	public function addButton(Components\Button $button) {
		$this->option[self::BUTTONS_OPTION]->addButton($button);
		return $this;
	}

	protected function setDefaults() {
		return array(
		    self::HEADER => 'Actions',
		    self::BUTTONS_OPTION => new Components\ButtonsContainer()
		);
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		if (array_key_exists(self::BUTTONS_OPTION, $this->option) === FALSE) {
			throw new Grid_Exception('Option \DataGrid\ButtonColumn::BUTTONS_OPTION is required.');
		}
		if (!$this->option[self::BUTTONS_OPTION] instanceof Components\ButtonsContainer) {
			throw new Grid_Exception('Option \DataGrid\ButtonColumn::BUTTONS_OPTION must be instance of Components\ButtonsContainer.');
		}
		if (array_key_exists(self::HEADER, $this->option) === FALSE) {
			throw new Grid_Exception('Option \Mesour\DataGrid\Column\Button::HEADER is required.');
		}
		$this->option[self::BUTTONS_OPTION]->setPresenter($this->grid->presenter);
		return array('class' => 'act buttons-count-' . count($this->option[self::BUTTONS_OPTION]));
	}

	public function getHeaderContent() {
		return $this->getTranslator() ? $this->getTranslator()->translate($this->option[self::HEADER]) : $this->option[self::HEADER];
	}

	public function getBodyAttributes($data) {
		return array('class' => 'right-buttons');
	}

	public function getBodyContent($data) {
		$count = $this->option[self::BUTTONS_OPTION]->getButtonsCount();
		$container = Html::el('span', array('class' => 'buttons-count-' . $count));
		if($this->getTranslator()) {
			$this->option[self::BUTTONS_OPTION]->setTranslator($this->getTranslator());
		}
		$container->setHtml($this->option[self::BUTTONS_OPTION]->create($data));
		return $container;
	}

}