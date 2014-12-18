<?php

namespace Mesour\DataGrid\Column;

use \Nette\Utils\Html,
    Mesour\DataGrid\Grid_Exception,
    Mesour\DataGrid\Components\StatusButton;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Status extends BaseOrdering {

	/**
	 * Possible option key
	 */
	const BUTTONS = 'buttons';

	static public $no_active_class = 'no-active-button';

	public function setButtons(array $buttons) {
		$this->option[self::BUTTONS] = $buttons;
	}

	public function addButton(StatusButton $button) {
		$this->option[self::BUTTONS][] = $button;
	}

	protected function setDefaults() {
		return array(
		    self::BUTTONS => array()
		);
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		if (array_key_exists(self::HEADER, $this->option) === FALSE) {
			throw new Grid_Exception('Option \DataGrid\Column\Status::HEADER is required.');
		}
		return array('class' => 'act buttons-count-1');
	}

	public function getHeaderContent() {
		return parent::getHeaderContent();
	}

	public function getBodyAttributes($data) {
		if (!isset($data[$this->option[self::ID]])) {
			throw new Grid_Exception('Column "' . $this->option[self::ID] . '" does not exist in data.');
		}
		$class = 'status-buttons';
		$exception = 'Option \DataGrid\StatusColumn::BUTTONS must be array contains instances of Components\StatusButton.';
		if (!is_array($this->option[self::BUTTONS])) {
			throw new Grid_Exception($exception);
		}
		$active_count = 0;
		foreach ($this->option[self::BUTTONS] as $button) {
			if (!$button instanceof StatusButton) {
				throw new Grid_Exception($exception);
			}
			if ($button->isActive($this->option[self::ID], $data)) {
				$class .= ' is-' . $button->getStatus();
				$active_count++;
			}
		}
		if ($active_count === 0) {
			$class .= ' ' . self::$no_active_class;
		}
		return parent::mergeAttributes($data, array('class' => $class));
	}

	public function getBodyContent($data) {
		$buttons = '';
		$active_count = 0;
		foreach ($this->option[self::BUTTONS] as $button) {
			if ($button->isActive($this->option[self::ID], $data)) {
				$button->setPresenter($this->getGrid()->presenter);
				if ($this->getTranslator()) {
					$button->setTranslator($this->getTranslator());
				}
				$buttons .= $button->create($data) . ' ';
				$active_count++;
			}
		}
		$container = Html::el('div', array('class' => 'thumbnailx buttons-count-' . $active_count));
		$container->setHtml($buttons);
		return $container;
	}

}