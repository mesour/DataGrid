<?php

namespace DataGrid\Column;

use \Nette\Utils\Html,
    \DataGrid\Grid_Exception;

/**
 * Description of \DataGrid\Column\Date
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class Date extends BaseOrdering {

	/**
	 * Possible option key
	 */
	const FORMAT = 'format',
	    TIME_FORMAT = 'time_format',
	    EDITABLE = 'editable';

	public function setFormat($format) {
		$this->option[self::FORMAT] = $format;
		return $this;
	}

	public function setTimeFormat($time_format) {
		$this->option[self::TIME_FORMAT] = $time_format;
		return $this;
	}

	public function setEditable($editable) {
		$this->option[self::EDITABLE] = (bool)$editable;
		return $this;
	}

	protected function setDefaults() {
		return array(
		    self::TIME_FORMAT => '',
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
			throw new Grid_Exception('Option \DataGrid\DateColumn::TEXT is required.');
		}
		if (array_key_exists(self::FORMAT, $this->option) === FALSE) {
			throw new Grid_Exception('Option \DataGrid\DateColumn::FORMAT is required.');
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
			if(!empty($this->option[self::TIME_FORMAT])) {
				$date_format = $this->formatToJqueryUiFormat(trim(str_replace($this->option[self::TIME_FORMAT], '', $this->option[self::FORMAT])));
				$time_format = $this->formatToJqueryUiFormat($this->option[self::TIME_FORMAT]);
			} else {
				$date_format = $this->formatToJqueryUiFormat($this->option[self::FORMAT]);
				$time_format = '';
			}
			$span->addAttributes(array(
			    'data-editable' => $this->option[self::ID],
			    'data-editable-type' => 'date',
			    'data-date-format' => $date_format,
			    'data-time-format' => $time_format
			));
		}

		$span->setText($this->getParsedValue($data));
		return $span;
	}

	public function getParsedValue($data) {
		parent::createBody($data);
		if (is_numeric($this->data[$this->option[self::ID]])) {
			$date = new \DateTime();
			$date->setTimestamp($this->data[$this->option[self::ID]]);
		} else {
			$date = new \DateTime($this->data[$this->option[self::ID]]);
		}
		return $date->format($this->option[self::FORMAT]);
	}

	private function formatToJqueryUiFormat($php_format) {
		$symbols = array(
			// Day
		    'd' => 'dd',
		    'D' => 'D',
		    'j' => 'd',
		    'l' => 'DD',
		    'N' => '',
		    'S' => '',
		    'w' => '',
		    'z' => 'o',
			// Week
		    'W' => '',
			// Month
		    'F' => 'MM',
		    'm' => 'mm',
		    'M' => 'M',
		    'n' => 'm',
		    't' => '',
			// Year
		    'L' => '',
		    'o' => '',
		    'Y' => 'yy',
		    'y' => 'y',
			// Time
		    'a' => '',
		    'A' => '',
		    'B' => '',
		    'g' => 'h',
		    'G' => 'H',
		    'h' => 'hh',
		    'H' => 'HH',
		    'i' => 'mm',
		    's' => 'ss',
		    'u' => ''
		);
		$jqueryui_format = "";
		$escaping = false;
		for ($i = 0; $i < strlen($php_format); $i++) {
			$char = $php_format[$i];
			if ($char === '\\') // PHP date format escaping character
			{
				$i++;
				if ($escaping) $jqueryui_format .= $php_format[$i];
				else $jqueryui_format .= '\'' . $php_format[$i];
				$escaping = true;
			} else {
				if ($escaping) {
					$jqueryui_format .= "'";
					$escaping = false;
				}
				if (isset($symbols[$char]))
					$jqueryui_format .= $symbols[$char];
				else
					$jqueryui_format .= $char;
			}
		}
		return $jqueryui_format;
	}

}