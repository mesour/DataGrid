<?php

namespace DataGrid\Extensions;

use DataGrid\Grid_Exception,
    Nette\Localization\ITranslator;

/**
 * @author jazby <jan.lorenc@jazby.net>
 * @package Mesour DataGrid
 */
class Translator extends BaseControl implements ITranslator {

	private $locales = array();
	private $locale;

	/**
	 * Translates the given string.
	 * @param  string $message
	 * @param  int $count
	 * @return string
	 */
	function translate($message, $count = null) {
		return isset($this->locales[$message]) ? $this->locales[$message] : $message;
	}

	/**
	 * @param $languageFile - Set language file
	 * @param null $customDir - Set custom directory (directory where you have translates from grid)
	 * @throws Grid_Exception
	 */
	function setLocale($languageFile, $customDir = null) {
		if(strpos($languageFile, ".php") === FALSE ) {
			$languageFile .= ".php";
		}
		if (is_null($customDir)) {
			$customDir = __DIR__ . '/templates/../../locales';
		}
		$file = $customDir . "/" . $languageFile;
		if (!is_file($file)) {
			throw new Grid_Exception('Locale file "' . $file . '" does not exist or is not readable.');
		}
		$this->locales = require($file);

		if (!is_array($this->locales)) {
			throw new Grid_Exception('DataGrid could not parse locales file.');
		}

		$this->locale = pathinfo($languageFile, PATHINFO_FILENAME);
	}

	/**
	 * Get current locale
	 * @return mixed
	 */
	function getLocale() {
		return $this->locale;
	}
}