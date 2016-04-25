<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Renderer;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
interface IGridRenderer extends Mesour\Components\Utils\IString
{

	public function getComponent($type);

	public function setComponent($type, $component);

	/**
	 * @return Mesour\Components\Utils\Html
	 */
	public function getWrapper();

	public function renderGrid();

	public function renderPager();
	
	public function renderEditable();

	public function renderSelection();

	public function renderFilter();

	public function renderExport();

	public function render();

}
