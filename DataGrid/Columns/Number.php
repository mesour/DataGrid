<?php

namespace DataGrid\Column;

use \Nette\Utils\Html,
    \DataGrid\Grid_Exception;

/**
 * Description of \DataGrid\Column\Number
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class Number extends BaseOrdering {

	/**
	 * Possible option key
	 */
	const DECIMALS = 'decimals',
	    DEC_POINT = 'dec_point',
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

	public function setThousandsSeparator($thousands_sep = ',') {
		$this->option[self::THOUSANDS_SEP] = $thousands_sep;
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
		    self::EDITABLE => TRUE
		);
	}

	/**
	 * Create HTML header
	 * 
	 * @return \Nette\Utils\Html
	 * @throws \DataGrid\Grid_Exception
	 */
	public function createHeader() {
		parent::createHeader();
		$th = Html::el('th');
		if (array_key_exists(self::TEXT, $this->option) === FALSE) {
			throw new Grid_Exception('Option \DataGrid\NumberColumn::TEXT is required.');
		}
		$this->addHeaderOrdering($th);
		return $th;
	}

	/**
	 * Create HTML body
	 *
	 * @param mixed $data
	 * @param string $container
	 * @return Html|void
	 * @throws Grid_Exception
	 */
	public function createBody($data, $container = 'td') {
		parent::createBody($data);

		$span = Html::el($container);
		if (isset($this->data[$this->option[self::ID]]) === FALSE && is_null($this->data[$this->option[self::ID]]) === FALSE) {
			throw new Grid_Exception('Column ' . $this->option[self::ID] . ' does not exists in DataSource.');
		}

		if ($this->grid->isEditable() && $this->option[self::EDITABLE]) {
			$span->addAttributes(array(
			    'data-editable' => $this->option[self::ID],
			    'data-editable-type' => 'number',
			    'data-separator' => $this->option[self::THOUSANDS_SEP],
			));
		}

		$span->setText($this->getParsedValue($data));
		return $span;
	}

	public function getParsedValue($data) {
		parent::createBody($data);
		return number_format($this->data[$this->option[self::ID]], $this->option[self::DECIMALS], $this->option[self::DEC_POINT], $this->option[self::THOUSANDS_SEP]);
	}

}