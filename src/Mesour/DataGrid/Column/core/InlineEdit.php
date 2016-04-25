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
abstract class InlineEdit extends Filtering implements IInlineEdit
{

	private $editable = true;

	private $reference;

	public function getBodyAttributes($data, $need = true, $rawData = [])
	{
		$attributes = parent::getBodyAttributes($data, $need, $rawData);
		if ($this->hasEditable()) {
			$source = $this->getGrid()->getSource();
			$dataStructure = $source->getDataStructure();
			if ($dataStructure->hasColumn($this->getName())) {
				$column = $dataStructure->getColumn($this->getName());
				if (
					$column instanceof Mesour\Sources\Structures\Columns\ManyToOneColumnStructure
					|| $column instanceof Mesour\Sources\Structures\Columns\OneToOneColumnStructure
				) {
					$value = $data[$column->getReferencedColumn()];
					if(!$value) {
						$attributes['data-grid-add'] = 'true';
					}
				}
			}
			if (!isset($value)) {
				$value = $data[$this->getName()];
			}
			$attributes = array_merge(
				$attributes, [
				'data-grid-editable' => $this->getName(),
				'data-grid-value' => $value,
				'data-grid-id' => $data[$this->getGrid()->getSource()->getPrimaryKey()],
			]
			);
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
