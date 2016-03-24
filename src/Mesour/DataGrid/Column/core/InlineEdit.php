<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Column;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
abstract class InlineEdit extends Filtering implements IInlineEdit
{

	private $editable = true;

	private $reference;

	public function getBodyAttributes($data, $need = true, $rawData = [])
	{
		$attributes = parent::getBodyAttributes($data, $need, $rawData);
		if ($this->hasEditable() && $this->reference) {
			$attributes = array_merge($attributes, [
				'data-editable-related' => $this->reference,
			]);
		}
		return parent::mergeAttributes($data, $attributes);
	}

	public function setEditable($editable = true)
	{
		$this->editable = (bool) $editable;
		return $this;
	}

	public function hasEditable()
	{
		if (!$this->editable) {
			return false;
		}
		$editable = $this->getGrid()->getExtension('IEditable', false);
		return $editable instanceof Mesour\DataGrid\Extensions\Editable\IEditable
		&& $editable->isAllowed()
		&& !$editable->isDisabled();
	}

	public function setReference($table)
	{
		$this->reference = (string) $table;
		return $this;
	}

	public function getReference()
	{
		return $this->reference;
	}

}
