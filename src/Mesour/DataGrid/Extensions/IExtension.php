<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
interface IExtension extends Mesour\Components\Control\IControl
{

	/**
	 * @return bool
	 */
	public function isDisabled();

	public function setDisabled($disabled = true);

	/**
	 * @param IExtension $extension
	 * @param null $name
	 * @return mixed
	 */
	public function createInstance(IExtension $extension, $name = null);

	public function gridCreate($data = []);

	public function afterGetCount($count);

	public function beforeFetchData($data = []);

	public function afterFetchData($currentData, $data = [], $rawData = []);

	public function attachToRenderer(Mesour\DataGrid\Renderer\IGridRenderer $renderer, $data = [], $rawData = []);

	public function reset($hard = false);

}
