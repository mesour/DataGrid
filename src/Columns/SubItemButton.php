<?php

namespace Mesour\DataGrid\Column;

use Mesour\DataGrid\Components\Button;
use Mesour\DataGrid\Components\Link;
use \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class SubItemButton extends Base {

	const NAME = 'name',
	    OPENED = 'opened',
	    TWO_ROWS = 'two-rows',
	    KEY = 'key';

	public function setName($name) {
		$this->option[self::NAME] = $name;
		return $this;
	}

	public function setKey($key) {
		$this->option[self::KEY] = $key;
		return $this;
	}

	public function setOpened($opened = TRUE) {
		$this->option[self::OPENED] = $opened;
		return $this;
	}

	public function setTwoRows($two_rows = TRUE) {
		$this->option[self::TWO_ROWS] = $two_rows;
		return $this;
	}

	protected function setDefaults() {
		return array(
		    self::OPENED => FALSE,
		    self::KEY => -1,
		    self::TWO_ROWS => FALSE,
		    self::NAME => NULL,
		);
	}

	public function getHeaderAttributes() {
		return array();
	}

	public function getHeaderContent() {
		return NULL;
	}

	public function getBodyAttributes($data) {
		$attributes = array('colspan' => $data, 'class' => 'subgrid-button');
		if($this->option[self::TWO_ROWS]) {
			$attributes['rowspan'] = 2;
		}
		return parent::mergeAttributes($data, $attributes);
	}

	public function getBodyContent($data) {
		$button = new Button();
		$button->setPresenter($this->grid->presenter)
		    ->setType('btn-info btn-xs')
		    ->setClassName('mesour-ajax')
		    ->addAttribute('href', $this->grid['subitem']->link('toggleItem!', array($this->option[self::KEY], $this->option[self::NAME])));
		if($this->option[self::OPENED]) {
			$button->setIcon('glyphicon-minus')
			    ->setTitle('Disable sub item');
		} else {
			$button->setIcon('glyphicon-plus')
			    ->setTitle('Enable sub item');
		}
		return $button;
	}

}