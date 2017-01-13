<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Renderer;

use Mesour;

/**
 * @author Matouš Němec (http://mesour.com)
 *
 * @method Mesour\DataGrid\Column\IColumn getColumn()
 */
class GridListRenderer extends Mesour\Table\Render\Lists\ListRenderer
{

	/**
	 * @var Mesour\UI\Button
	 */
	private $createButton;

	/**
	 * @var Mesour\UI\Button
	 */
	private $removeButton;

	protected $liAttributes = [];

	public function __construct(Mesour\Table\Render\IColumn $column)
	{
		parent::__construct($column);

		$this->getWrapperPrototype()
			->class('mesour-datagrid-list', true);
	}

	private function getCreateButton()
	{
		if (!$this->createButton) {
			$this->createButton = new Mesour\UI\Button('_create_button');
			$this->createButton->setIcon('plus')
				->setClassName('');
			$this->createButton->setAttribute('data-grid-is-add', 'true');
		}
		return $this->createButton;
	}

	private function createRemoveButton()
	{
		$deleteButton = new Mesour\UI\Button('_delete_button');
		$deleteButton->setIcon('times')
			->setClassName('');

		$deleteButton->setAttribute('data-grid-is-remove', 'true');

		return $deleteButton;
	}

	public function render()
	{
		$wrapper = $this->getWrapperPrototype();
		$column = $this->getColumn();
		$hasEditable = $column instanceof Mesour\DataGrid\Column\IInlineEdit && $column->hasEditable();
		if ($hasEditable) {
			$editableField = $column->getEditableField();
		}

		$this->onRender($this, $wrapper);

		foreach ($this->getItems() as $item) {
			$li = $this->createLiPrototype();
			if ($this->getCallback()) {
				Mesour\Components\Utils\Helpers::invokeArgs($this->getCallback(), [$this, $li, $item[0], $item[1], $item[2]]);
			} else {
				$li->add($item[1]);

				$attributes = $column->getEditableAttributes($item[0], $this->liAttributes, $item[2]);
				$li->addAttributes($attributes);

				if ($hasEditable) {
					$li->add('&nbsp;');
					$li->add($column->createEditButton());
				}

				if (isset($editableField) && $editableField->hasRemoveRowEnabled()) {
					$li->add('&nbsp;');
					$li->add($this->createRemoveButton());
				}
			}
			$this->onRenderRow($this, $li, $item[0], $item[1]);
			$wrapper->add($li);
		}

		if (isset($editableField) && $editableField->hasCreateNewRowEnabled()) {
			$createLi = $this->createLiPrototype();
			$createLi->add($this->getCreateButton());
			$wrapper->add($createLi);
		}

		return $wrapper;
	}

	public function __clone()
	{
		parent::__clone();

		$this->liAttributes = [];

		if ($this->createButton) {
			$this->createButton = clone $this->createButton;
			$this->createButton->setAttributes([]);
		}
		if ($this->removeButton) {
			$this->removeButton = clone $this->removeButton;
			$this->removeButton->setAttributes([]);
		}
	}

}
