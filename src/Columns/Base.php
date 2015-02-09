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

	/**
	 *
	 * @var \Mesour\DataGrid\Grid
	 */
	protected $grid;

	/**
	 * @param string $key
	 * @param string $value
	 * @param bool $append
	 * @return $this
	 */
	public function addAttribute($key, $value, $append = FALSE) {
		if($append && isset($this->option[self::ATTRIBUTES][$key])) {
			$this->option[self::ATTRIBUTES][$key] = $this->option[self::ATTRIBUTES][$key] . ' ' . $value;
		} else {
			$this->option[self::ATTRIBUTES][$key] = $value;
		}
		return $this;
	}

	/**
	 * @param IComponent $grid
	 * @return $this
	 */
	public function setGridComponent(IComponent $grid) {
		$this->grid = $grid;
		return $this;
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
			return isset($this->option['id']) ? $this->option['id'] : NULL;
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
		$isnt_special = (!$this instanceof Actions && !$this instanceof Sortable);
		if ($isnt_special && array_key_exists('id', $this->option) === FALSE) {
			throw new Grid_Exception('Column ID can not be empty.');
		}
		if ($isnt_special && (array_key_exists('header', $this->option) === FALSE
		    	|| array_key_exists('header', $this->option) && is_null($this->option['header']))) {
			$this->option['header'] = $this->option['id'];
		}
	}

}