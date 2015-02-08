<?php
/**
 * Mesour Nette DataGrid
 *
 * Documentation here: http://grid.mesour.com
 *
 * @license LGPL-3.0 and BSD-3-Clause
 * @copyright (c) 2013 - 2014 Matous Nemec <matous.nemec@mesour.com>
 */

namespace Mesour\DataGrid;

use Nette\Application\UI\Form;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class ExtendedGrid extends BasicGrid {

	/**
	 * Event which is triggered when filter data
	 *
	 * @var array
	 */
	public $onFilter = array();

	public function enableFilter(Form $filer_form = NULL, $template = NULL, $date = 'Y-m-d') {
		new Extensions\Filter($this, 'filter');
		if (!is_null($filer_form)) {
			$this['filter']->setFilterForm($filer_form, $template);
		}
		$this['filter']->setDateFormat($date);
	}

	/**
	 * Get filter values for manual filtering
	 * If filter form is not set return NULL
	 *
	 * @return NULL|Array
	 */
	public function getFilterValues() {
		return $this['filter']->getFilterValues();
	}

	public function enableExport($cache_dir, $file_name = NULL, array $columns = array(), $delimiter = ",") {
		new Extensions\Export($this, 'export');
		$this['export']->setCacheDir($cache_dir);
		$this['export']->setFileName($file_name);
		$this['export']->setColumns($columns);
		$this['export']->setDelimiter($delimiter);
	}
}