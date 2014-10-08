<?php

namespace DataGrid\Column;

use \Nette\Utils\Html,
    \DataGrid\Grid_Exception,
    \DataGrid\Components;

/**
 * Description of \DataGrid\Column\Button
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class Button extends Base {

	/**
	 * Possible option key
	 */
	const TEXT = 'text',
	    BUTTONS_OPTION = 'buttons_option';

	public function setText($text) {
		$this->option[self::TEXT] = $text;
		return $this;
	}

	public function setButtons(Components\ButtonsContainer $container) {
		$this->option[self::BUTTONS_OPTION] = $container;
		return $this;
	}

	public function addButton(Components\Button $button) {
		if(!isset($this->option[self::BUTTONS_OPTION])) {
			$this->option[self::BUTTONS_OPTION] = new Components\ButtonsContainer();
		}
		$this->option[self::BUTTONS_OPTION]->addButton($button);
		return $this;
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		if (array_key_exists(self::BUTTONS_OPTION, $this->option) === FALSE) {
			throw new Grid_Exception('Option \DataGrid\ButtonColumn::BUTTONS_OPTION is required.');
		}
		if (!$this->option[self::BUTTONS_OPTION] instanceof Components\ButtonsContainer) {
			throw new Grid_Exception('Option \DataGrid\ButtonColumn::BUTTONS_OPTION must be instance of Components\ButtonsContainer.');
		}
		if (array_key_exists(self::TEXT, $this->option) === FALSE) {
			throw new Grid_Exception('Option \DataGrid\ButtonColumn::TEXT is required.');
		}
		$this->option[self::BUTTONS_OPTION]->setPresenter($this->grid->presenter);
		return array('class' => 'act buttons-count-' . count($this->option[self::BUTTONS_OPTION]));
	}

	public function getHeaderContent() {
		return $this->option[self::TEXT];
	}

	public function getBodyAttributes($data) {
		return array('class' => 'right-buttons');
	}

	public function getBodyContent($data) {
		$count = $this->option[self::BUTTONS_OPTION]->getButtonsCount();
		$container = Html::el('div', array('class' => 'thumbnailx buttons-count-' . $count));

		$container->setHtml($this->option[self::BUTTONS_OPTION]->create($data));
		return $container;
	}

}