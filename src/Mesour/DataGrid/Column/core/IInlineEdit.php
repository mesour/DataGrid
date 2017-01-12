<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Column;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
interface IInlineEdit extends Mesour\Components\ComponentModel\IComponent, IColumn
{

	/**
	 * @param bool $editable
	 * @return mixed
	 */
	public function setEditable($editable = true);

	/**
	 * @return bool
	 */
	public function hasEditable();

	public function setReference($table);

	public function getReference();

	/**
	 * @return Mesour\Editable\Structures\Fields\IStructureElementField|Mesour\Editable\Structures\Fields\IStructureField
	 */
	public function getEditableField();

	/**
	 * @return Mesour\UI\Button
	 */
	public function createEditButton();

	/**
	 * @param mixed $data
	 * @param array $attributes
	 * @param mixed $itemData
	 * @return array
	 */
	public function getEditableAttributes($data, array $attributes = [], $itemData = []);

}
