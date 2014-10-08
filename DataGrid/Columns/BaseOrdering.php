<?php

namespace DataGrid\Column;

use \Nette\Utils\Html;

/**
 * Description of \DataGrid\Column\BaseOrdering
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
abstract class BaseOrdering extends Base {

	/**
	 * Possible option key
	 */
	const ID = 'id',
	    TEXT = 'text',
	    ORDERING = 'ordering';

	public function setId($id) {
		$this->option[self::ID] = $id;
		return $this;
	}

	public function setText($text) {
		$this->option[self::TEXT] = $text;
		return $this;
	}

	public function setOrdering($ordering = TRUE) {
		$this->option[self::ORDERING] = $ordering;
		return $this;
	}

	public function getHeaderContent() {
		if (!isset($this->option[self::ORDERING])) {
			$this->option[self::ORDERING] = TRUE;
		}
		if (isset($this->option[self::ORDERING]) && $this->option[self::ORDERING]) {
			$ordering = $this->getGrid()->getOrdering($this->option[self::ID]);
			$link = Html::el('a', array('href' => $this->getGrid()->link('ordering!', $this->option[self::ID]), 'class' => 'ajax ordering' . (!is_null($ordering) ? (' ' . strtolower($ordering)) : '')));
			$link->setText($this->option[self::TEXT]);
			$link->add(Html::el('span', array('class' => 'glyphicon no-sort'))->setHtml('&nbsp;'));
			$link->add(Html::el('span', array('class' => 'glyphicon glyphicon-sort-by-alphabet')));
			$link->add(Html::el('span', array('class' => 'glyphicon glyphicon-sort-by-alphabet-alt')));
			return $link;
		} else {
			return $this->option[self::TEXT];
		}
	}

}