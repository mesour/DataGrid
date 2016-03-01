<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid;

use Mesour;
use Mesour\DataGrid\Extensions;


/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
abstract class SubItemGrid extends ExtendedGrid
{

	private $subItems = [];

	/**
	 * @return Extensions\SubItem\SubItemExtension
	 */
	public function enableSubItems()
	{
		$this->addComponent(new Mesour\UI\Control, 'subCol');
		$this->addComponent(new Mesour\UI\Control, 'subButton');
		return $this->getExtension('ISubItem');
	}

	public function create($data = [])
	{
		$subItem = $this->getExtension('ISubItem');
		$this->onRender[] = function (Mesour\UI\DataGrid $dataGrid, $rawData, $fullData) use ($subItem) {
			if ($subItem instanceof Extensions\SubItem\ISubItem && $subItem->hasSubItems() && $subItem->isAllowed() && !$subItem->isDisabled()) {
				$opened = $subItem->getOpened();
				foreach ($fullData as $key => $rowData) {
					foreach ($opened as $name => $item) {
						if (in_array((string)$key, $item['keys'])) {
							/** @var Extensions\SubItem\Items\Item $instance */
							$instance = $item['item'];
							$t_key = $instance->getTranslatedKey($key);
							$instance->invoke([$rawData[$key]], $name, $t_key);
							if (isset($this[$name . $t_key])) {
								$this->subItems[$key][$name] = $this[$name . $t_key];
							} else {
								$this->subItems[$key][$name] = true;
							}
						}
					}
				}
			}
		};
		$this->onAfterRenderRow[] = function ($body, $key, $rawData, $rowData) use ($subItem) {
			if ($subItem instanceof Extensions\SubItem\ISubItem && $subItem->isAllowed() && !$subItem->isDisabled()) {
				foreach ($subItem->getItems() as $name => $item) {
					/** @var Extensions\SubItem\Items\Item $item */
					$item->check($rawData);
					if (isset($this->subItems[$key][$name])) {
						$this->addOpenedSubItemRow($body, $rowData, $name, $key, $item, $rawData);
					} else {
						$this->addClosedSubItemRow($body, $rowData, $name, $key, $item, $rawData);
					}
				}
			}
		};

		return parent::create($data);
	}

	protected function setSubItemColumn(Column\IColumn $column, $name, $header = null)
	{
		$column->setHeader($header);
		return $this['subCol'][$name] = $column;
	}

	protected function setSubItemButton(Column\IColumn $column, $name, $header = null)
	{
		$column->setHeader($header);
		return $this['subButton'][$name] = $column;
	}

	protected function addClosedSubItemRow(Mesour\Table\Render\Body &$body, $rowData, $name, $key, Extensions\SubItem\Items\Item $item, $rawData)
	{
		if ($item->isDisabled() || !$item->isAllowed()) {
			return;
		}

		$columnsCount = count($this->getColumns());
		$oldName = $name;

		$name = $this->getExtension('ISubItem')->getItem($oldName)->getName() . $key;
		$column = new Column\SubItemButton($name);
		$column->setColumnName($oldName)
			->setKey($key);

		$this->setSubItemButton($column, $name);

		$rendererFactory = $this->getRendererFactory();
		$cell = $rendererFactory->createCell(1, $column, $rawData);
		$row = $rendererFactory->createRow($rowData, $rawData);
		$row->addCell($cell);
		$columnsCount--;

		$subItemColumn = new Column\SubItem();
		$subItemColumn->setText($this->getExtension('ISubItem')->getItem($oldName)->getDescription());

		$this->setSubItemColumn($subItemColumn, $name);
		$cell = $rendererFactory->createCell($columnsCount, $subItemColumn, $rawData);

		$row->setAttribute('class', 'no-sort');
		$row->addCell($cell);
		$body->addRow($row);
	}

	protected function addOpenedSubItemRow(Mesour\Table\Render\Body &$body, $rowData, $name, $key, Extensions\SubItem\Items\Item $item, $rawData)
	{
		if ($item->isDisabled() || !$item->isAllowed()) {
			return;
		}

		$columnsCount = count($this->getColumns());
		$columnsCount--;
		$oldName = $name;

		$name = $this->getExtension('ISubItem')->getItem($oldName)->getName() . $key;
		$content = $this->getExtension('ISubItem')->getItem($oldName)->render($key, $rowData, $rawData);

		$column = new Column\SubItemButton($name);
		$column->setColumnName($oldName)
			->setKey($key)
			->setTwoRows()
			->setOpened(true);

		$this->setSubItemButton($column, $name);

		$rendererFactory = $this->getRendererFactory();

		$subItem = new Column\SubItem;
		$subItem->setText($content);

		$this->setSubItemColumn($subItem, $name);
		$cell = $rendererFactory->createCell($columnsCount, $subItem, $rawData);

		$row = $rendererFactory->createRow($rowData, $rawData);
		$row->setAttribute('class', 'no-sort');
		$row->addCell($cell);

		$_row = $rendererFactory->createRow($rowData, $rawData);
		$description = new Column\SubItem;
		$description->setText($this->getExtension('ISubItem')->getItem($oldName)->getDescription());

		$this->setSubItemColumn($description, $name . '_des');
		$_cell = $rendererFactory->createCell($columnsCount, $description, $rawData);

		$_row->addCell($rendererFactory->createCell(1, $column, $rawData));
		$_row->addCell($_cell);
		$_row->setAttribute('class', 'no-sort');
		$body->addRow($_row);
		$body->addRow($row);
	}

}