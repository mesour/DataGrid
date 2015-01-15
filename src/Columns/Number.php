<?php

namespace Mesour\DataGrid\Column;

use Mesour\DataGrid\Grid_Exception;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Number extends BaseOrdering {

	/**
	 * Possible option key
	 */
	const DECIMALS = 'decimals',
	    DEC_POINT = 'dec_point',
	    THOUSANDS_SEP = 'thousands_sep',
	    EDITABLE = 'editable',
	    FILTERING = 'filtering';

	public function setDecimals($decimals) {
		$this->option[self::DECIMALS] = $decimals;
		return $this;
	}

	public function setDecimalPoint($dec_point = '.') {
		$this->option[self::DEC_POINT] = $dec_point;
		return $this;
	}

	public function setThousandsSeparator($thousands_sep = ',') {
		$this->option[self::THOUSANDS_SEP] = $thousands_sep;
		return $this;
	}

	public function setFiltering($filtering) {
		$this->option[self::FILTERING] = (bool)$filtering;
		return $this;
	}

	public function setEditable($editable) {
		$this->option[self::EDITABLE] = (bool)$editable;
		return $this;
	}

	protected function setDefaults() {
		return array(
		    self::DECIMALS => 0,
		    self::DEC_POINT => '.',
		    self::THOUSANDS_SEP => ',',
		    self::EDITABLE => TRUE,
		    self::FILTERING => TRUE
		);
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
			);
		}
		$attributes['class'] = 'type-number';
		return parent::mergeAttributes($data, $attributes);
	}

	public function getBodyContent($data) {
		return number_format($data[$this->option[self::ID]], $this->option[self::DECIMALS], $this->option[self::DEC_POINT], $this->option[self::THOUSANDS_SEP]);
	}

}