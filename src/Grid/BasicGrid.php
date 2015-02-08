<?php
/**
 * Mesour Nette DataGrid
 *
 * Documentation here: http://grid.mesour.com
 *
 * @license LGPL-3.0 and BSD-3-Clause
 * @copyright (c) 2013 - 2015 Matous Nemec <matous.nemec@mesour.com>
 */

namespace Mesour\DataGrid;

use Mesour\DataGrid\Column\IColumn;
use \Nette\ComponentModel\IContainer,
    \Nette\Localization\ITranslator,
    Mesour\DataGrid\Render\Table\RendererFactory;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class BasicGrid extends BaseGrid {

	/**
	 * @var ITranslator
	 */
	protected $translator;

	/**
	 * @var NULL|string
	 */
	private $empty_text = NULL;

	/**
	 * Limit for one page
	 *
	 * @var int
	 */
	protected $page_limit = NULL;

	/**
	 * Contains TRUE if before render called
	 *
	 * @var bool
	 */
	private $called_before_render = FALSE;

	/**
	 * Event which is triggered when column was edit
	 *
	 * @var array
	 */
	public $onEditCell = array();

	/**
	 * Event which is triggered after sort rows
	 *
	 * @var array
	 */
	public $onSort = array();

	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		new Extensions\Ordering($this, 'ordering');
		new Extensions\Pager($this, 'pager');
		new Extensions\Selection($this, 'selection');
	}

	/**
	 * @param $column_name
	 * @param null|string $header
	 * @return Column\Status
	 */
	public function addStatus($column_name, $header = NULL) {
		return $this->addColumn(new Column\Status, $column_name, $header);
	}

	/**
	 * @param $column_name
	 * @param null|string $header
	 * @return Column\Date
	 */
	public function addDate($column_name, $header = NULL) {
		return $this->addColumn(new Column\Date, $column_name, $header);
	}

	/**
	 * @param $column_name
	 * @param null|string $header
	 * @return Column\Number
	 */
	public function addNumber($column_name, $header = NULL) {
		return $this->addColumn(new Column\Number, $column_name, $header);
	}

	/**
	 * @param $column_name
	 * @param null|string $header
	 * @return Column\Text
	 */
	public function addText($column_name, $header = NULL) {
		return $this->addColumn(new Column\Text, $column_name, $header);
	}

	/**
	 * @param $column_name
	 * @param null|string $header
	 * @return Column\Image
	 */
	public function addImage($column_name, $header = NULL) {
		return $this->addColumn(new Column\Image, $column_name, $header);
	}

	/**
	 * @param $column_name
	 * @param null|string $header
	 * @return Column\Container
	 */
	public function addContainer($column_name, $header = NULL) {
		return $this->addColumn(new Column\Container, $column_name, $header);
	}

	/**
	 * @param null|string $header
	 * @return Column\Actions
	 */
	public function addActions($header = NULL) {
		return $this->addColumn(new Column\Actions, NULL, $header);
	}

	protected function addColumn(IColumn $column, $column_name = NULL, $header = NULL) {
		if(!is_null($header)) {
			$column->setHeader($header);
		}
		if(!is_null($column_name)) {
			$column->setId($column_name);
		}
		return parent::addColumn($column);
	}

	public function setDefaultOrder($key, $sorting = 'ASC') {
		$this['ordering']->setDefaultOrder($key, $sorting);
	}

	public function disableOrdering($disabled = TRUE) {
		$this['ordering']->setDisabled($disabled);
	}

	public function enableMultiOrdering() {
		$this['ordering']->enableMulti();
	}

	public function enablePager($page_limit = 20, $max_for_normal = 15, $edge_page_count = 3, $middle_page_count = 2) {
		$this['pager']->enable();
		$this->page_limit = $page_limit;
		$this['pager']->setMaxForNormal($max_for_normal)
		    ->setEdgePageCount($edge_page_count)
		    ->setMiddlePageCount($middle_page_count);
	}

	public function enableEditableCells() {
		new Extensions\Editable($this, 'editable');
	}

	public function enableSorting() {
		new Extensions\Sortable($this, 'sortable');
	}

	/**
	 * @return Extensions\SelectionLinks
	 */
	public function enableRowSelection() {
		$this['selection']->enable();
		$this['selection']->setPrimaryKey($this->getPrimaryKey());
		return $this['selection']->getLinks();
	}

	/**
	 * @param ITranslator $translator
	 */
	public function setTranslator(ITranslator $translator) {
		$this->translator = $translator;
	}

	public function getTranslator() {
		return $this->translator instanceof ITranslator ? $this->translator : null;
	}

	public function setEmptyText($empty_text) {
		$this->empty_text = $this->getTranslator() ? $this->getTranslator()->translate($empty_text) : $empty_text;
	}

	public function getPageLimit() {
		return $this->page_limit;
	}

	public function reset($ordering = FALSE) {
		if ($this['pager']->isEnabled()) {
			$this['pager']->reset(0);
		}
		if(isset($this['subitem'])) {
			$this['subitem']->reset();
		}
		if($ordering) {
			$this['ordering']->reset();
		}
	}

	public function render($return = FALSE) {
		$this->template->grid_dir = __DIR__;

		if(!$this->rendererFactory) {
			$this->setRendererFactory(new RendererFactory);
		}

		$this->template->content = $this->createBody('table table-striped table-condensed');

		$this->template->setFile(dirname(__FILE__) . '/Grid.latte');
		if($return) {
			return $this->template;
		}
		$this->template->render();
	}

	/**
	 * Must called before create body
	 */
	protected function beforeCreate() {
		if ($this['selection']->isEnabled()) {
			$this->column_arr[-1] = $this['selection']->getSelectionColumn();
		}
		if (isset($this['sortable'])) {
			$this->column_arr[-2] = new Column\Sortable();
		}
		parent::beforeCreate();
		ksort($this->column_arr);
	}

	/**
	 * Must called before render header
	 */
	protected function beforeRender() {
		$this['ordering']->applyOrder();
		$this->template->setTranslator($this["translator"]);

		if ($this->called_before_render === TRUE) {
			return FALSE;
		}
		parent::beforeRender();

		if (isset($this['filter'])) {
			$this['filter']->applyFilter();
		}
		$this->updateCounts();

		$this->called_before_render = TRUE;
	}

	protected function updateCounts() {
		$count = parent::updateCounts();
		if ($this['pager']->isEnabled()) {
			$this['pager']->setCounts($count, $this->page_limit);
			$this->getDataSource()->applyLimit($this->page_limit, ($this['pager']->getCurrentPageIndex()) * $this->page_limit);
		}
	}

	public function createBody($table_class = 'table') {
		$table = parent::createBody($table_class);

		$sub_items = array();
		$full_data = $this->getDataSource()->fetchAll();

		if(isset($this['subitem']) && $this['subitem']->hasSubItems()) {
			foreach ($full_data as $key => $rowData) {
				foreach($this['subitem']->getOpened() as $name => $item) {
					if(in_array($key, $item['keys'])) {
						$t_key = $item['item']->getTranslatedKey($key);
						$item['item']->invoke(array($this[$name.$t_key], $rowData));
						$sub_items[$key][$name] = $this[$name.$t_key];
					}
				}
			}
		}

		$header = $this->rendererFactory->createHeader();
		$header->setAttributes(array('class' => 'grid-header'));
		foreach ($this->getColumns() as $column) {
			$header->addCell($this->rendererFactory->createHeaderCell($column));
		}
		$table->setHeader($header);

		$body = $this->rendererFactory->createBody();
		if (isset($this['sortable'])) {
			$body->setAttributes(array(
			    'class' => 'sortable',
			    'data-sort-href' => $this['sortable']->link('sortData!')
			));
		}
		if ($this->hasEmptyData()) {
			$this->addRow($body, count($this->getColumns()), TRUE);
		} else {
			foreach ($full_data as $key => $rowData) {
				$this->addRow($body, $rowData);
				if(isset($sub_items[$key])) {
					$this->addRow($body, $rowData, $sub_items[$key], $key);
				} elseif(isset($this['subitem'])) {
					$this->addRow($body, $rowData, $this['subitem']->getItemsCount(), $key);
				}
			}
		}
		$table->setBody($body);
		return $table;
	}

	protected function addRow(Render\Body &$body, $rowData, $empty = FALSE, $key = 0) {
		$row = $this->rendererFactory->createRow($rowData);
		if (!$this->hasEmptyData()) {
			$row->setAttributes(array(
			    'id' => $this->getLineId($rowData)
			));
		}

		if ($empty !== FALSE) {
			$columns_count = count($this->column_arr);
			if (!is_bool($empty)) {
				if(is_array($empty)) {
					$columns_count--;
					foreach($empty as $name => $item) {
						$content = $this['subitem']->getItem($name)->render($key);
						$column = new Column\SubItemButton();
						$column->setGridComponent($this)
						    ->setName($name)
						    ->setKey($key)
						    ->setTwoRows()
						    ->setOpened(TRUE);
						$cell = $this->rendererFactory->createCell(1, $column);

						$empty_column = new Column\SubItem(array(
						    Column\SubItem::TEXT => $content
						));
						$_row = $this->rendererFactory->createRow($rowData);
						$description = new Column\SubItem(array(
						    Column\SubItem::TEXT => $this['subitem']->getItem($name)->getDescription()
						));
						$_cell = $this->rendererFactory->createCell($columns_count, $description);
						$_row->addCell($cell);
						$_row->addCell($_cell);
						$_row->addAttribute('class', 'no-sort');
						$body->addRow($_row);
					}
				} elseif(is_numeric($empty)) {
					foreach($this['subitem']->getNames() as $name) {
						$column = new Column\SubItemButton();
						$column->setGridComponent($this)
						    ->setName($name)
							->setKey($key);
						$cell = $this->rendererFactory->createCell(1, $column);
						$row->addCell($cell);
						$columns_count--;
						$empty_column = new Column\SubItem(array(
						    Column\SubItem::TEXT => $this['subitem']->getItem($name)->getDescription()
						));
					}
				} else {
					$empty_column = new Column\SubItem(array(
					    Column\SubItem::TEXT => $empty
					));
				}

			} else {
				$empty_column = new Column\EmptyData(array(
				    Column\EmptyData::TEXT => $this->empty_text ? $this->empty_text : 'Nothing to display.'
				));
			}

			if(isset($empty_column)) {
				$cell = $this->rendererFactory->createCell($columns_count, $empty_column);
				$row->addCell($cell);
			}
			$row->addAttribute('class', 'no-sort ' . count($this->getColumns()));
		} else {
			foreach ($this->getColumns() as $column) {
				$row->addCell($this->rendererFactory->createCell($rowData, $column));
			}
		}

		$body->addRow($row);
		return $row;
	}
}