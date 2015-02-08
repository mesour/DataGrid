<?php

namespace Mesour\DataGrid\Column;

use Mesour\DataGrid\Grid_Exception;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Number extends Filter {

	/**
	 * Possible option key
	 */
	const DECIMALS = 'decimals',
	    DEC_POINT = 'dec_point',
	    UNIT = 'unit',
	    THOUSANDS_SEP = 'thousands_sep',
	    EDITABLE = 'editable';

	public function setDecimals($decimals) {
		$this->option[self::DECIMALS] = $decimals;
		return $this;
	}

	public function setDecimalPoint($dec_point = '.') {
		$this->option[self::DEC_POINT] = $dec_point;
		return $this;
	}

	public function setUnit($unit) {
		$this->option[self::UNIT] = $unit;
		return $this;
	}

	public function setThousandsSeparator($thousands_sep = ',') {
		$this->option[self::THOUSANDS_SEP] = $thousands_sep;
		return $this;
	}

	public function setEditable($editable) {
		$this->option[self::EDITABLE] = (bool)$editable;
		return $this;
	}

	protected function setDefaults() {
		return array_merge(parent::setDefaults(), array(
		    self::DECIMALS => 0,
		    self::UNIT => NULL,
		    self::DEC_POINT => '.',
		    self::THOUSANDS_SEP => ',',
		    self::EDITABLE => TRUE
		));
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		if (array_key_exists(self::HEADER, $this->option) === FALSE) {
			throw new Grid_Exception('Option ' . __CLASS__ . '::HEADER is required.');
		}
		return array(
		    'class' => 'grid-column-' . $this->option[self::ID]
		);
	}

	public function getHeaderContent() {
		return parent::getHeaderContent();
	}

	public function getBodyAttributes($data) {
		if (isset($data[$this->option[self::ID]]) === FALSE && is_null($data[$this->option[self::ID]]) === FALSE) {
			throw new Grid_Exception('Column ' . $this->option[self::ID] . ' does not exists in DataSource.');
		}

		$attributes = array();
		if (isset($this->grid['editable']) && $this->option[self::EDITABLE]) {
			$attributes = array(
			    'data-editable' => $this->option[self::ID],
			    'data-editable-type' => 'number',
			    'data-separator' => $this->option[self::THOUSANDS_SEP],
			    'data-unit' => is_null($this->option[self::UNIT]) ? '' : (' ' . $this->option[self::UNIT]),
			);
		}
		$attributes['class'] = 'type-number';
		return parent::mergeAttributes($data, $attributes);
	}

	public function getBodyContent($data) {
		return number_format($data[$this->option[self::ID]], $this->option[self::DECIMALS], $this->option[self::DEC_POINT], $this->option[self::THOUSANDS_SEP])
		. ($this->option[self::UNIT] ? (' ' . $this->option[self::UNIT]) : '');
	}

}