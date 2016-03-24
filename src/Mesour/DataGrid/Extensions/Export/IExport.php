<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\Export;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
interface IExport extends Mesour\DataGrid\Extensions\IExtension
{

	public function setFileName($fileName);

	/**
	 * @param string $delimiter
	 * @return mixed
	 */
	public function setDelimiter($delimiter = ',');

	public function setCacheDir($dir);

	public function setColumns(array $columns = []);

	/**
	 * @return Mesour\UI\Button|Mesour\UI\DropDown
	 */
	public function getExportButton();

	public function hasExport(Mesour\Components\ComponentModel\IContainer $column);

	public function handleExport($type = 'all', array $selectedIds = []);

}
