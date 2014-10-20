<?php

namespace DataGrid\Column;

use \Nette\Utils\Html,
    \DataGrid\Grid_Exception,
    \DataGrid\Components\StatusButton;

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

	private $active_count = 0;

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
		if (array_key_exists(self::TEXT, $this->option) === FALSE) {
			throw new Grid_Exception('Option \DataGrid\TextColumn::TEXT is required.');
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
		$class = 'right-buttons ' . self::$no_active_class;
		foreach($this->option[self::BUTTONS] as $button) {
			if (!$button instanceof StatusButton) {
				throw new Grid_Exception('Option \DataGrid\StatusColumn::BUTTONS must be array contains instances of Components\StatusButton.');
			}
			if($button->isActive($this->option[self::ID], $data)) {
				$class .= ' is-' . $button->getStatus();
				$this->active_count++;
			}
		}
		return array('class' => $class);
	}

	public function getBodyContent($data) {
		$buttons = '';
		$active_count = 0;
		foreach($this->option[self::BUTTONS] as $button) {
			if($button->isActive($this->option[self::ID], $data)) {
				$button->setPresenter($this->getGrid()->presenter);
				$buttons .= $button->create($data) . ' ';
				$active_count++;
			}
		}
		$container = Html::el('div', array('class' => 'thumbnailx buttons-count-' . $active_count));
		$container->setHtml($buttons);
		return $container;
	}

}