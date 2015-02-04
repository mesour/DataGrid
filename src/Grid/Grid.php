<?php
/**
 * Mesour Nette DataGrid
 *
 * Documentation here: http://grid.mesour.com
 *
 * @license LGPL-3.0 and BSD-3-Clause
 * @copyright (c) 2013 - 2014 Matous Nemec <matous.nemec@mesour.com>
 */

namespace Mesour\DataGrid;

use Nette\Application\UI\Form,
    \Nette\ComponentModel\IContainer,
    \Nette\Localization\ITranslator,
    Mesour\DataGrid\Render\Table\RendererFactory;
use Nette\Utils\Callback;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class _Grid extends BaseGrid {

	/**
	 * @var ITranslator
	 */
	protected $translator;

	/**
	 * @var int
	 */
	private $sub_items = 0;

	/**
	 * @var NULL|string
	 */
	private $empty_text = NULL;

	/**
	 * Limit for one page
	 *
	 * @var int
	 */
	protected $page_limit;

	/**
	 * Contains TRUE if before render called
	 *
	 * @var bool
	 */
	private $called_before_render = FALSE;

	/**
	 * Event which is triggered after sort rows
	 *
	 * @var array
	 */
	public $onSort = array();

	/**
	 * Event which is triggered when column was edit
	 *
	 * @var array
	 */
	public $onEditCell = array();

	/**
	 * Event which is triggered when filter data
	 *
	 * @var array
	 */
	public $onFilter = array();

	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		new Extensions\Ordering($this, 'ordering');
	}

	public function addStatus($column_name, $header = NULL) {
		$column = new Column\Status();
		$column->setId($column_name)
		    ->setHeader($header);
		$this->column_arr[] = $column;
		return $column;
	}

	public function addDate($column_name, $header = NULL) {
		$column = new Column\Date();
		$column->setId($column_name)
		    ->setHeader($header);
		$this->column_arr[] = $column;
		return $column;
	}

	public function addNumber($column_name, $header = NULL) {
		$column = new Column\Number();
		$column->setId($column_name)
		    ->setHeader($header);
		$this->column_arr[] = $column;
		return $column;
	}

	public function addText($column_name, $header = NULL) {
		$column = new Column\Text();
		$column->setId($column_name)
		    ->setHeader($header);
		$this->column_arr[] = $column;
		return $column;
	}

	public function addImage($column_name, $header = NULL) {
		$column = new Column\Image();
		$column->setId($column_name)
		    ->setHeader($header);
		$this->column_arr[] = $column;
		return $column;
	}

	public function addContainer($column_name, $header = NULL) {
		$column = new Column\Container();
		$column->setId($column_name)
		    ->setHeader($header);
		$this->column_arr[] = $column;
		return $column;
	}

	public function addActions($header = NULL) {
		$column = new Column\Actions();
		$column->setHeader($header);
		$this->column_arr[] = $column;
		return $column;
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

	public function enableFilter(Form $filer_form = NULL, $template = NULL, $date = 'Y-m-d') {
		new Extensions\Filter($this, 'filter');
		if (!is_null($filer_form)) {
			$this['filter']->setFilterForm($filer_form, $template);
		}
		$this['filter']->setDateFormat($date);
	}

	/**
	 * Get filter values for manual filtering
	 * If filter form is not set return NULL
	 *
	 * @return NULL|Array
	 */
	public function getFilterValues() {
		return $this['filter']->getFilterValues();
	}

	public function enablePager($page_limit = 20, $max_for_normal = 15, $edge_page_count = 3, $middle_page_count = 2) {
		new Extensions\Pager($this, 'pager');
		$this->page_limit = $page_limit;
		$this['pager']->setMaxForNormal($max_for_normal)
		    ->setEdgePageCount($edge_page_count)
		    ->setMiddlePageCount($middle_page_count);
	}

	public function enableExport($cache_dir, $file_name = NULL, array $columns = array(), $delimiter = ",") {
		new Extensions\Export($this, 'export');
		$this['export']->setCacheDir($cache_dir);
		$this['export']->setFileName($file_name);
		$this['export']->setColumns($columns);
		$this['export']->setDelimiter($delimiter);
	}

	/**
	 * @return Extensions\SelectionLinks
	 */
	public function enableRowSelection() {
		new Extensions\Selection($this, 'selection');
		$this['selection']->setPrimaryKey($this->dataSource->getPrimaryKey());
		return $this['selection']->getLinks();
	}

	public function enableSorting() {
		new Extensions\Sortable($this, 'sortable');
	}

	public function enableEditableCells() {
		new Extensions\Editable($this, 'editable');
	}

	/**
	 * Pager have to be initialized before call this method
	 *
	 * @param $callback
	 * @experimental
	 */
	public function enableSubItem($callback) {
		//new Extensions\SubItem($this, 'subitem'.$this->sub_items);
		$name = 'subitem' . $this->sub_items;
		$this->addComponent(new Extensions\SubItem, $name);
		$this[$name]->setCallback($callback);
		$this->sub_items++;
	}

	public function getOpenedSubItems() {
		return array(0,1,2,3,4);
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

	protected function attached($presenter) {
		parent::attached($presenter);
		//$this->beforeCreate();
		////$this->beforeRender();
		/*foreach($this->getOpenedSubItems() as $i) {
			$full_data = $this->getDataSource()->fetchAll();
			foreach ($full_data as $key => $rowData) {
				for($i = 0; $i < $this->sub_items; $i++) {
					$sub_item = $this['subitem'.$i]->invoke(array($key, $rowData));
					$sub_items[$i][$key] = $sub_item;
				}
			}
		}*/
	}

	public function render() {
		$this->template->grid_dir = __DIR__;

		if(!$this->rendererFactory) {
			$this->setRendererFactory(new RendererFactory);
		}
		$this->template->content = $this->createBody('table table-striped table-condensed');

		$this->template->setFile(dirname(__FILE__) . '/Grid.latte');
		$this->template->render();
	}

	/**
	 * Must called before create body
	 */
	protected function beforeCreate() {
		if (isset($this['selection'])) {
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
		if (isset($this['pager'])) {
			$this['pager']->setCounts($count, $this->page_limit);
			$this->getDataSource()->applyLimit($this->page_limit, ($this['pager']->getCurrentPageIndex()) * $this->page_limit);
		}
	}

	public function createBody($table_class = 'table') {
		$table = parent::createBody($table_class);

		$sub_items = array();
		$full_data = $this->getDataSource()->fetchAll();

		/*foreach ($full_data as $key => $rowData) {
			for($i = 0; $i < $this->sub_items; $i++) {
				$sub_item = $this['subitem'.$i]->invoke(array($key, $rowData));
				$sub_items[$i][$key] = $sub_item;
			}
		}*/

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
				for($i = 0; $i < $this->sub_items; $i++) {
					$this->addRow($body, $rowData, $sub_items[$i][$key]);
				}
			}
		}
		$table->setBody($body);
		return $table;
	}

	protected function addRow(Render\Body &$body, $rowData, $empty = FALSE) {
		$row = $this->rendererFactory->createRow($rowData);
		if (!$this->hasEmptyData()) {
			$row->setAttributes(array(
			    'id' => $this->getLineId($rowData)
			));
		}

		if ($empty !== FALSE) {
			if (!is_bool($empty)) {
				if($empty instanceof self) {
					if(!$empty->rendererFactory) {
						$empty->setRendererFactory($this->rendererFactory);
					}
					$table = $empty->createBody('sub-item');
					$content = $table->create();
				} else {
					$content = $empty;
				}
				$empty_column = new Column\SubGrid(array(
				    Column\EmptyData::TEXT => $content
				));
			} else {
				$empty_column = new Column\EmptyData(array(
				    Column\EmptyData::TEXT => $this->empty_text ? $this->empty_text : 'Nothing to display.'
				));
			}

			$cell = $this->rendererFactory->createCell(count($this->column_arr), $empty_column);
			$row->addAttribute('class', 'no-sort ' . count($this->getColumns()));
			$row->addCell($cell);
		} else {
			foreach ($this->getColumns() as $column) {
				$row->addCell($this->rendererFactory->createCell($rowData, $column));
			}
		}

		$body->addRow($row);
		return $row;
	}
}