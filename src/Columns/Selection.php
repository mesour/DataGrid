<?php

namespace Mesour\DataGrid\Column;

use Mesour\DataGrid\Extensions\SelectionHelper;
use \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Selection extends Base {

	/**
	 * Possible option key
	 */
	const ID = 'id',
	    HELPERS = 'helpers';

	public function setId($id) {
		$this->option[self::ID] = $id;
		return $this;
	}

	/**
	 * @param $name
	 * @param $value
	 * @return $this
	 */
	public function addHelper($name, $value) {
		$helper = new SelectionHelper($name, $value);
		$this->option[self::HELPERS][] = $helper;
		return $this;
	}

	public function addDivider() {
		$this->option[self::HELPERS][] = 'divider';
		return $this;
	}

	protected function setDefaults() {
		return array_merge(parent::setDefaults(), array(
		    self::HELPERS => array()
		));
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		return array('class' => 'act act-select');
	}

	public function getHeaderContent() {
		$div = Html::el('div', array('class' => 'btn-group checkbox-selector'));

		$button = Html::el('button', array('type' => 'button'));
		$checkbox = Html::el('a', array('type' => 'button', 'class' => 'btn btn-xs btn-default main-checkbox'));
		$checkbox->add('&nbsp;&nbsp;&nbsp;&nbsp;');
		$button->add($checkbox);

		$button->class('btn btn-default btn-xs dropdown-toggle');
		$button->add(' ');
		$button->add(Html::el('span', array('class' => 'caret')));

		$ul = Html::el('ul', array('class' => 'dropdown-menu', 'role' => 'menu'));
		foreach ($this->option[self::HELPERS] as $i => $helper) {
			if($helper instanceof SelectionHelper) {
				if(!is_null($this->getTranslator())) {
					$helper->setTranslator($this->getTranslator());
				}
				$a = Html::el('a', array('href' => '#', 'data-select' => $helper->getValue()));
				$a->setText($helper->getName());
				$ul->add(Html::el('li')->add($a));
			} else {
				$ul->add(Html::el('li', array('class' => 'divider')));
			}
			if($i === count($this->option[self::HELPERS])-1) {
				$ul->add(Html::el('li', array('class' => 'divider')));
			}
		}
		$ul->add(Html::el('li')->add(Html::el('a', array('href' => '#', 'data-select' => 'inverse'))->setText($this->grid['translator']->translate('Inverse selection'))));
		$div->add($ul);

		$div->add($button);

		return $div;
	}

	public function getBodyAttributes($data) {
		return parent::mergeAttributes($data, array('class' => 'with-checkbox'));
	}

	public function getBodyContent($data) {
		$checkbox = Html::el('a', array('data-value' => $data[$this->option[self::ID]], 'class' => 'btn btn-default btn-xs select-checkbox'));
		$checkbox->add('&nbsp;&nbsp;&nbsp;&nbsp;');
		return $checkbox;
	}

}