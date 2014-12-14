<?php

namespace DataGrid\Column;

use \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class EmptyData extends Base {

	const TEXT = 'text';

	public function setText($text) {
		$this->option[self::TEXT] = $text;
		return $this;
	}

	public function getHeaderAttributes() {
		return array();
	}

	public function getHeaderContent() {
		return NULL;
	}

	public function getBodyAttributes($data) {
		return parent::mergeAttributes($data, array('colspan' => $data));
	}

	public function getBodyContent($data) {
		$text = Html::el('p', array('class' => 'empty-data'));
		$text->setText($this->getTranslator() ? $this->getTranslator()->translate($this->option[self::TEXT]) : $this->option[self::TEXT]);
		return $text;
	}

}