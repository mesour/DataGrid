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

		return $this->getEditableAttributes($data, $attributes);
	}

	/**
	 * @return Mesour\UI\Button
	 */
	public function createEditButton()
	{
		$editButton = new Mesour\UI\Button('_edit_button');
		$editButton->setIcon('pencil')
			->setClassName('');

		$editButton->setAttribute('data-grid-is-edit', 'true');

		return $editButton;
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

	/**
	 * @return Mesour\Editable\Structures\Fields\IStructureElementField|Mesour\Editable\Structures\Fields\IStructureField
	 */
	public function getEditableField()
	{
		/** @var Mesour\DataGrid\Extensions\Editable\EditableExtension $editable */
		$editable = $this->getGrid()->getExtension('IEditable');
		return $editable->getDataStructure()->getField($this->getName());
	}

	public function getEditableAttributes($data, array $attributes = [], $itemData = [])
	{
		if ($this->hasEditable()) {
			$value = null;
			$identifier = null;

			$dataStructure = $this->getGrid()->getSource()->getDataStructure();
			if ($dataStructure->hasColumn($this->getName())) {
				$column = $dataStructure->getColumn($this->getName());
				if (
					$column instanceof Mesour\Sources\Structures\Columns\ManyToOneColumnStructure
					|| $column instanceof Mesour\Sources\Structures\Columns\OneToOneColumnStructure
				) {
					$value = $data[$column->getReferencedColumn()];
					$identifier = $data[$this->getGrid()->getSource()->getPrimaryKey()];
				} elseif (
					$itemData && (
						$column instanceof Mesour\Sources\Structures\Columns\OneToManyColumnStructure
						|| $column instanceof Mesour\Sources\Structures\Columns\ManyToManyColumnStructure
					)
				) {
					$value = $itemData[$column->getTableStructure()->getPrimaryKey()];
				}

				$value = $value ?: $data[$this->getName()];

				if (
					!$column instanceof Mesour\Sources\Structures\Columns\OneToManyColumnStructure
					&& !$column instanceof Mesour\Sources\Structures\Columns\ManyToManyColumnStructure
					&& ((!is_array($value) && $value !== null) || (is_array($value) && count($value) > 0))
				) {
					$attributes['data-grid-edit'] = 'true';
				}
			}

			$valueAttributes = $attributes + [
				'data-grid-value' => $value ?: $data[$this->getName()],
			];
			if ($itemData) {
				return $valueAttributes;
			}

			return $valueAttributes + [
					'data-grid-editable' => $this->getName(),
					'data-grid-id' => $identifier ?: $identifier = $data[$this->getGrid()->getSource()->getPrimaryKey()],
				];
		}
		return [];
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
