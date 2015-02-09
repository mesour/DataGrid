<?php

namespace Mesour\DataGrid\Column;

use \Nette\Utils\Html;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
abstract class BaseOrdering extends Base {

	/**
	 * Possible option key
	 */
	const ID = 'id',
	    HEADER = 'header',
	    ORDERING = 'ordering';

	public function setId($id) {
		$this->option[self::ID] = $id;
		return $this;
	}

	public function setHeader($header) {
		$this->option[self::HEADER] = $header;
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
			$ordering = $this->grid['ordering']->getOrdering($this->option[self::ID]);
			$link = Html::el('a', array('href' => $this->grid['ordering']->link('ordering!', $this->option[self::ID]), 'class' => 'mesour-ajax ordering' . (!is_null($ordering) ? (' ' . strtolower($ordering)) : '')));
			$link->setText($this->getTranslator() ? $this->getTranslator()->translate($this->option[self::HEADER]) : $this->option[self::HEADER]);
			$link->add(Html::el('span', array('class' => 'glyphicon no-sort'))->setHtml('&nbsp;'));
			if($this instanceof Number || $this instanceof Date) {
				$link->add(Html::el('span', array('class' => 'order-asc glyphicon glyphicon-sort-by-order')));
				$link->add(Html::el('span', array('class' => 'order-desc glyphicon glyphicon-sort-by-order-alt')));
			} else if($this instanceof Status) {
				$link->add(Html::el('span', array('class' => 'order-asc glyphicon glyphicon-sort-by-attributes')));
				$link->add(Html::el('span', array('class' => 'order-desc glyphicon glyphicon-sort-by-attributes-alt')));
			} else {
				$link->add(Html::el('span', array('class' => 'order-asc glyphicon glyphicon-sort-by-alphabet')));
				$link->add(Html::el('span', array('class' => 'order-desc glyphicon glyphicon-sort-by-alphabet-alt')));
			}
			return $link;
		} else {
			return $this->getTranslator() ? $this->getTranslator()->translate($this->option[self::HEADER]) : $this->option[self::HEADER];
		}
	}

}