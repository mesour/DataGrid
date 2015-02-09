<?php
/**
 * Mesour Nette DataGridTree
 *
 * Documentation here: http://grid.mesour.com
 *
 * @license LGPL-3.0 and BSD-3-Clause
 * @copyright (c) 2013 - 2015 Matous Nemec <matous.nemec@mesour.com>
 */

namespace Mesour\DataGrid;

use Mesour\DataGrid\Render\Tree\RendererFactory;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class GridTree extends ExtendedGrid {

	protected $main_parent_value = 0;

	public function render() {
		$this->template->grid_dir = __DIR__;

		if(!$this->getRendererFactory()) {
			$this->setRendererFactory(new RendererFactory);
		}
		$this->template->content = $this->createBody();

		$this->template->setFile(dirname(__FILE__) . '/Grid.latte');
		$this->template->render();
	}

	/**
	 * @param string $table_class
	 * @return Render\Renderer
	 * @throws Grid_Exception
	 */
	public function createBody($table_class = 'tree-grid') {
		$table = parent::createBody($table_class);

		$header = $this->rendererFactory->createHeader();
		$header->setAttributes(array('class' => 'grid-header'));
		foreach ($this->getColumns() as $column) {
			$header->addCell($this->rendererFactory->createHeaderCell($column));
		}
		$table->setHeader($header);

		$data = $this->getDataSource()->fetchAssoc();
		$body = $this->rendererFactory->createBody();
		$body_attributes = array(
		    'class' => 'grid-ul'
		);
		if (isset($this['sortable'])) {
			$body_attributes['class'] = 'grid-ul sortable';
			$body_attributes['data-sort-href'] = $this['sortable']->link('sortData!');
		}
		$body->setAttributes($body_attributes);
		if (!empty($data)) {
			if (!isset($data[$this->main_parent_value])) {
				throw new Grid_Exception('Main parent value key does not exist in data.');
			}
			foreach ($data[$this->main_parent_value] as $rowData) {
				$this->addTreeRow($body, $rowData, $data);
			}
		}
		$table->setBody($body);
		return $table;
	}

	private function rowsWalkRecursive(Render\Row &$row, $rowId, $groupData) {
		$sub_body = $this->rendererFactory->createBody();
		$sub_body->setAttributes(array(
		    'class' => 'grid-ul'
		));
		foreach ($groupData[$rowId] as $rowData) {
			$this->addTreeRow($sub_body, $rowData, $groupData);
		}
		$row->setBody($sub_body);
	}

	protected function addTreeRow(Render\Body &$body, $rowData, $groupData) {
		$row = $this->addRow($body, $rowData);
		$sub_id = $rowData[$this->getPrimaryKey()];
		if (isset($groupData[$sub_id])) {
			$this->rowsWalkRecursive($row, $sub_id, $groupData);
		}
	}
}