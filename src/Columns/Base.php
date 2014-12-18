<?php

namespace Mesour\DataGrid\Column;

use Mesour\DataGrid\Components\Link,
    \Nette\ComponentModel\IComponent,
    Mesour\DataGrid\Grid_Exception,
    Mesour\DataGrid\Setting;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
abstract class Base extends Setting implements IColumn {

	const ATTRIBUTES = 'attributes';

	public function setAttributes(array $attributes) {
		$this->option[self::ATTRIBUTES] = $attributes;
		return $this;
	}

	public function addAttribute($key, $value) {
		$this->option[self::ATTRIBUTES][$key] = $value;
		return $this;
	}

	/**
	 *
	 * @var \Mesour\DataGrid\Grid
	 */
	protected $grid;

	/**
	 * @param \Nette\ComponentModel\IComponent $grid
	 */
	public function setGridComponent(IComponent $grid) {
		$this->grid = $grid;
	}

	protected function getGrid() {
		return $this->grid;
	}

	public function getId() {
		return isset($this->option['id']) ? $this->option['id'] : NULL;
	}

	public function getHeader() {
		if (isset($this->option['header']) && $this->getTranslator()) {
			return $this->getTranslator()->translate($this->option['header']);
		} elseif (isset($this->option['header'])) {
			return $this->option['header'];
		} else {
			return NULL;
		}
	}

	public function isEditable() {
		return isset($this->option['editable']) ? $this->option['editable'] : FALSE;
	}

	public function hasFiltering() {
		return isset($this->option['filtering']) ? $this->option['filtering'] : FALSE;
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		return array();
	}

	public function getBodyAttributes($data) {
		if (!empty($this->option[self::ATTRIBUTES]) && is_array($this->option[self::ATTRIBUTES])) {
			foreach ($this->option[self::ATTRIBUTES] as $key => $value) {
				$this->option[self::ATTRIBUTES][$key] = Link::parseValue($value, $data);
			}
			return $this->option[self::ATTRIBUTES];
		}
		return array();
	}

	protected function mergeAttributes($data, array $current) {
		$base = self::getBodyAttributes($data);
		if (isset($base['class']) && isset($current['class'])) {
			$base['class'] = $base['class'] . ' ' . $current['class'];
		}
		return array_merge($current, $base);
	}

	/**
	 * Fix column option
	 *
	 * @throws Grid_Exception
	 */
	protected function fixOption() {
		$isnt_special = (!$this instanceof Button && !$this instanceof Sortable && !$this instanceof Dropdown);
		if ($isnt_special && array_key_exists('id', $this->option) === FALSE) {
			throw new Grid_Exception('Column ID can not be empty.');
		}
		if ($isnt_special && array_key_exists('header', $this->option) === FALSE) {
			$this->option['header'] = $this->option['id'];
		}
	}

}