<?php

namespace Mesour\DataGrid\Column;

use Mesour\DataGrid\Grid_Exception;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Date extends Filter {

	/**
	 * Possible option key
	 */
	const FORMAT = 'format',
	    EDITABLE = 'editable';

    public function getTemplateFile() {
        return 'DateDropdown.latte';
    }

	public function setFormat($format) {
		$this->option[self::FORMAT] = $format;
		return $this;
	}

	public function setEditable($editable) {
		$this->option[self::EDITABLE] = (bool)$editable;
		return $this;
	}

	protected function setDefaults() {
		return array_merge(parent::setDefaults(), array(
		    self::EDITABLE => TRUE
		));
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		if (array_key_exists(self::HEADER, $this->option) === FALSE) {
			throw new Grid_Exception('Option ' . __CLASS__ . '::HEADER is required.');
		}
		if (array_key_exists(self::FORMAT, $this->option) === FALSE) {
			throw new Grid_Exception('Option ' . __CLASS__ . '::FORMAT is required.');
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
			    'data-editable-type' => 'date',
			    'data-date-format' => $this->formatToMomentJsFormat($this->option[self::FORMAT])
			);
		}
		$attributes['class'] = 'type-date';
		return parent::mergeAttributes($data, $attributes);
	}

	public function getBodyContent($data) {
        if(!$data[$this->option[self::ID]]) {
            return '-';
        }
		if (is_numeric($data[$this->option[self::ID]])) {
			$date = new \DateTime();
			$date->setTimestamp($data[$this->option[self::ID]]);
		} else {
			$date = new \DateTime($data[$this->option[self::ID]]);
		}
		return $date->format($this->option[self::FORMAT]);
	}

	static public function formatToMomentJsFormat($php_format) {
		$symbols = array(
			// Day
		    'd' => 'DD',
		    'D' => 'ddd',
		    'j' => 'D',
		    'l' => 'dddd',
		    'N' => 'E',
		    'S' => '',
		    'w' => 'e',
		    'z' => 'DDD',
			// Week
		    'W' => 'W',
			// Month
		    'F' => 'MMMM',
		    'm' => 'MM',
		    'M' => 'MMM',
		    'n' => 'M',
		    't' => '',
			// Year
		    'L' => '',
		    'o' => '',
		    'Y' => 'YYYY',
		    'y' => 'YY',
			// Time
		    'a' => 'a',
		    'A' => 'A',
		    'B' => 'SSS',
		    'g' => 'h',
		    'G' => 'H',
		    'h' => 'hh',
		    'H' => 'HH',
		    'i' => 'mm',
		    's' => 'ss',
		    'u' => ''
		);
		$js_format = "";
		$escaping = false;
		for ($i = 0; $i < strlen($php_format); $i++) {
			$char = $php_format[$i];
			if ($char === '\\') // PHP date format escaping character
			{
				$i++;
				if ($escaping) $js_format .= $php_format[$i];
				else $js_format .= '\'' . $php_format[$i];
				$escaping = true;
			} else {
				if ($escaping) {
					$js_format .= "'";
					$escaping = false;
				}
				if (isset($symbols[$char]))
					$js_format .= $symbols[$char];
				else
					$js_format .= $char;
			}
		}
		return $js_format;
	}

}