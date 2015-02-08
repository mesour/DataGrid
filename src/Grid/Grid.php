<?php
/**
 * Mesour Nette DataGrid
 *
 * Documentation here: http://grid.mesour.com
 *
 * @license LGPL-3.0 and BSD-3-Clause
 * @copyright (c) 2013 - 2015 Matous Nemec <matous.nemec@mesour.com>
 */

namespace Mesour\DataGrid;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Grid extends ExtendedGrid {

	/**
	 * @return Extensions\SubItem
	 */
	public function enableSubItems() {
		new Extensions\SubItem($this, 'subitem', $this->page_limit);
		return $this['subitem'];
	}

}