<?php

namespace DataGrid\Utils;
use DataGrid\Grid_Exception;

/**
 * Description of \DataGrid\Utils\Option
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
abstract class Option {

	/**
	 * Option for this column
	 *
	 * @var Array
	 */
	protected $option = array();

	/**
	 * @param array $option
	 * @throws Grid_Exception
	 */
	public function __construct(array $option = array()) {
		$defaults = $this->setDefaults();
		if(!is_array($defaults)) {
			throw new Grid_Exception('Protected function setDefaults must return an array.');
		}
		$this->option = $defaults;
		if(!empty($option)) {
			$this->option = array_merge($defaults, $option);
		}
	}

	protected function setDefaults() {
		return array();
	}

}