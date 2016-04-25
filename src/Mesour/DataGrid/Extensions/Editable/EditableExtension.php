<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\Editable;

use Mesour;
use Mesour\DataGrid\Extensions;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class EditableExtension extends Mesour\UI\Editable implements IEditable
{

	/**
	 * @var Mesour\Components\Utils\Html
	 */
	private $createdEditable;

	/**
	 * @return Mesour\DataGrid\ExtendedGrid
	 */
	public function getGrid()
	{
		return $this->getParent();
	}

	public function getDataStructure()
	{
		try {
			return parent::getDataStructure();
		} catch (Mesour\InvalidStateException $e) {
			$this->setDataStructure(
				Mesour\Editable\Structures\DataStructure::fromSource($this->getGrid()->getSource())
			);
		}
		return parent::getDataStructure();
	}

	public function gridCreate($data = [])
	{

	}

	public function createInstance(Extensions\IExtension $extension, $name = null)
	{
	}

	public function afterGetCount($count)
	{
		$editableDataStructure = $this->getDataStructure();
		$dataStructure = $this->getGrid()->getSource()->getDataStructure();
		foreach ($this->getGrid()->getColumns() as $column) {
			if (
				$column instanceof Mesour\DataGrid\Column\IInlineEdit
				&& $column->hasEditable()
				&& !$editableDataStructure->hasField($this->getName())
			) {
				if ($dataStructure->hasColumn($column->getName())) {
					$this->determineAndAttachField(
						$editableDataStructure,
						$column,
						$dataStructure->getColumn($column->getName())
					);
				} else {
					$this->determineAndAttachField($editableDataStructure, $column);
				}
			}
		}
	}

	private function determineAndAttachField(
		Mesour\Editable\Structures\IDataStructure $structure,
		Mesour\DataGrid\Column\IColumn $column,
		Mesour\Sources\Structures\Columns\IColumnStructure $columnStructure = null)
	{
		if ($column instanceof Mesour\DataGrid\Column\Text) {
			$structure->addText($column->getName(), $column->getHeader());
		} elseif ($column instanceof Mesour\DataGrid\Column\Number) {
			$structure->addNumber($column->getName(), $column->getHeader())
				->setDecimalPoint($column->getDecimalPoint())
				->setDecimals($column->getDecimals())
				->setThousandSeparator($column->getThousandSeparator())
				->setUnit($column->getUnit());
		} elseif ($column instanceof Mesour\DataGrid\Column\Date) {
			$structure->addDate($column->getName(), $column->getHeader())
				->setFormat($column->getFormat());
		}
	}

	public function beforeFetchData($data = [])
	{
		$this->createdEditable = $this->create();
		$this->getGrid()->setAttribute('data-mesour-editable', $this->createLinkName());

		$editableDataStructure = $this->getDataStructure();
		foreach ($this->getGrid()->getColumns() as $column) {
			if (
				$column instanceof Mesour\DataGrid\Column\IInlineEdit
				&& !$column->hasEditable()
				&& $editableDataStructure->hasField($column->getName())
			) {
				$editableDataStructure->getField($column->getName())->setDisabled(true);
			}
		}
	}

	public function afterFetchData($currentData, $data = [], $rawData = [])
	{
	}

	public function attachToRenderer(Mesour\DataGrid\Renderer\IGridRenderer $renderer, $data = [], $rawData = [])
	{
		$renderer->setComponent('editable', $this->createdEditable);
	}

	public function reset($hard = false)
	{
	}

}
