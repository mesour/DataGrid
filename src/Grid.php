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
    \Nette\Application\UI\Control,
    \Nette\Localization\ITranslator;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Grid extends Control {

	/**
	 * DataGrid name
	 *
	 * @var string
	 */
	private $name;

	private $grid_name;

	/**
	 * Columns array
	 *
	 * @var array
	 */
	private $column_arr = array();

	/**
	 * Total count of result
	 *
	 * @var int|FALSE
	 */
	private $total_count = FALSE;

	/**
	 * Limit for one page
	 *
	 * @var int
	 */
	private $page_limit;

	/**
	 * Contains TRUE if before render called
	 *
	 * @var bool
	 */
	private $called_before_render = FALSE;

	/**
	 * Data source
	 *
	 * @var \Mesour\DataGrid\IDataSource
	 */
	private $data_source;

	/**
	 * Current count before apply limit
	 *
	 * @var integer
	 */
	private $count;

	private $real_column_names = NULL;

	private $main_parent_value = 0;

	/**
	 * @var ITranslator
	 */
	protected $translator;

	/**
	 * @var NULL|string
	 */
	private $empty_text = NULL;

	/**
	 * @var callback|NULL
	 */
	private $sub_grid;

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

	/**
	 * @var bool
	 */
	static public $js_draw = TRUE;

	/**
	 * @var bool
	 */
	static public $css_draw = TRUE;

	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);

		$this->name = $name;
		new Extensions\Ordering($this, 'ordering');
		new Extensions\Translator($this, 'translator');
		$this["translator"]->setLocale("en");
	}

	static public function disableJsDraw() {
		self::$js_draw = FALSE;
	}

	static public function disableCssDraw() {
		self::$css_draw = FALSE;
	}

	/**
	 * @return String
	 */
	public function getGridName() {
		if(!$this->grid_name) {
			$this->grid_name = str_replace(':', '-', $this->presenter->getName() . $this->name);
		}
		return $this->grid_name;
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

	/**
	 * @return Array
	 */
	public function getColumns() {
		return $this->column_arr;
	}

	/**
	 * @return IDataSource
	 * @throws Grid_Exception
	 */
	public function getDataSource() {
		if(!$this->data_source) {
			throw new Grid_Exception('Data source is not set. Use setDataSource.');
		}
		return $this->data_source;
	}

	/**
	 * Get count without where and limit
	 *
	 * @return Integer
	 */
	public function getTotalCount() {
		if (!$this->total_count === FALSE) {
			$this->setTotalCount();
		}
		return $this->total_count;
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
		$this['selection']->setPrimaryKey($this->data_source->getPrimaryKey());
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
	public function enableSubGrid($callback) {
		$this->sub_grid = $callback;
		for($x = 0; $x < ($this->page_limit ? $this->page_limit : $this->getTotalCount()); $x++) {
			new self($this, $this->getName() . $x);
		}
	}

	public function setMainParentValue($value) {
		$this->main_parent_value = $value;
	}

	public function setEmptyText($empty_text) {
		$this->empty_text = $this->getTranslator() ? $this->getTranslator()->translate($empty_text) : $empty_text;
	}

	/**
	 * @param $languageFile - Set language file
	 * @param null $customDir - Set custom directory (directory where you have translates from grid)
	 * @throws Grid_Exception
	 */
	public function setLocale($languageFile, $customDir = null) {
		$this["translator"]->setLocale($languageFile, $customDir);
	}

	/**
	 * Sets translate adapter.
	 * @return self
	 */
	public function setTranslator(ITranslator $translator)
	{
		$this->translator = $translator;
	}

	public function getTranslator()
	{
		return $this->translator instanceof ITranslator ? $this->translator : null;
	}

	public function fetchAll() {
		return $this->getDataSource()->fetchAll();
	}

	public function getRealColumnNames() {
		if (is_null($this->real_column_names)) {
			$this->real_column_names = array_keys($this->getDataSource()->fetch());
		}
		return $this->real_column_names;
	}

	public function hasEmptyData() {
		$column_names = $this->getRealColumnNames();
		return empty($column_names) ? TRUE : FALSE;
	}

	/**
	 * @return \Nette\Utils\Paginator|NULL
	 */
	public function getPaginator() {
		if(isset($this['pager'])) {
			$this->beforeRender();
			return $this['pager']->getPaginator();
		}
		return NULL;
	}

	public function setDataSource(IDataSource & $data_source) {
		$this->data_source = $data_source;
	}

	public function isSubGrid() {
		return $this->parent instanceof self;
	}

	/**
	 * @return IDataSource
	 */
	public function getCurrentDataSource() {
		$this->beforeRender();
		return $this->getDataSource();
	}

	public function render() {
		$this->template->grid_dir = __DIR__;

		$factory = new Render\Table\RendererFactory($this);
		$table = $this->createBody($factory);
		$this->template->content = $table;
		$this->template->locale = $this['translator']->getLocale();

		$this->template->setFile(dirname(__FILE__) . '/Grid.latte');
		$this->template->render();
	}

	private function getLineId($data) {
		if(!isset($data[$this->data_source->getPrimaryKey()])) {
			throw new Grid_Exception('Primary key "' . $this->data_source->getPrimaryKey() . '" does not exists in data. For change use setPrimaryKey on DataSource.');
		}
		return $this->getName() . '-' . $data[$this->data_source->getPrimaryKey()];
	}

	/**
	 * Must called before create body
	 */
	private function beforeCreate() {
		if (isset($this['selection'])) {
			$this->column_arr[-1] = $this['selection']->getSelectionColumn();
		}
		if (isset($this['sortable'])) {
			$this->column_arr[-2] = new Column\Sortable();
		}
		foreach ($this->column_arr as $column) {
			$column->setGridComponent($this);
			if ($column instanceof Column\BaseOrdering && $this['ordering']->isDisabled()) {
				$column->setOrdering(FALSE);
			}
		}
		ksort($this->column_arr);
		$this->checkEmptyColumns();
	}

	/**
	 * Must called before render header
	 */
	private function beforeRender() {
		$this['ordering']->applyOrder();
		$this->template->setTranslator($this["translator"]);
		$this->template->locale = $this["translator"]->getLocale();

		if ($this->called_before_render === TRUE) {
			return FALSE;
		}
		if (isset($this['filter'])) {
			$this['filter']->applyFilter();
		}
		$this->count = $this->getDataSource()->count();

		if (isset($this['pager'])) {
			$this['pager']->setCounts($this->count, $this->page_limit);
			$this->getDataSource()->applyLimit($this->page_limit, ($this['pager']->getCurrentPageIndex()) * $this->page_limit);
		}
		$this->called_before_render = TRUE;
	}

	/**
	 * Check if columns are empty, if empty, set default Text columns by DB
	 */
	private function checkEmptyColumns() {
		if (empty($this->column_arr)) {
			foreach ($this->getRealColumnNames() as $key) {
				$column = new Column\Text(array(
				    Column\Text::ID => $key,
				));
				$column->setGridComponent($this);
				$this->column_arr[] = $column;
			}
		}
	}

	/**
	 * Set total count of data grid
	 */
	private function setTotalCount() {
		$this->total_count = $this->getDataSource()->getTotalCount();
	}

	public function createBody(Render\IRendererFactory $factory) {
		$this->beforeCreate();
		$table = $factory->createTable();
		if ($factory instanceof Render\Tree\RendererFactory) {
			$table_class = 'tree-grid';
		} else {
			$table_class = 'table table-striped table-condensed';
		}
		$table->setAttributes(array(
		    'class' => $table_class
		));

		$this->beforeRender();

		$sub_grids = array();
		$full_data = $this->getDataSource()->fetchAll();


		foreach ($full_data as $key => $rowData) {
			if(is_callable($this->sub_grid)) {
				$sub_grid = call_user_func($this->sub_grid, $this[$this->getName() . $key], $rowData);
				if(!$sub_grid instanceof self) {
					throw new Grid_Exception('Sub grid have to be instance of ' . __CLASS__ . '.');
				}
				$sub_grids[$key] = $sub_grid;
			}
		}

		$header = $factory->createHeader();
		$header->setAttributes(array('class' => 'grid-header'));
		foreach ($this->getColumns() as $column) {
			$header->addCell($factory->createHeaderCell($column));
		}
		$table->setHeader($header);

		if ($factory instanceof Render\Tree\RendererFactory) {
			$data = $this->getDataSource()->fetchAssoc();
			$body = $factory->createBody();
			$body_attributes = array(
			    'class' => 'grid-ul'
			);
			if (isset($this['sortable'])) {
				$body_attributes['class'] = 'grid-ul sortable';
				$body_attributes['data-sort-href'] = $this['sortable']->link('sortData!');
			}
			$body->setAttributes($body_attributes);
			if(!empty($data)) {
				if(!isset($data[$this->main_parent_value])) {
					throw new Grid_Exception('Main parent value key does not exist in data.');
				}
				foreach ($data[$this->main_parent_value] as $rowData) {
					$this->addTreeRow($factory, $body, $rowData, $data);
				}
			}
			$table->setBody($body);
		} else {
			$body = $factory->createBody();
			if (isset($this['sortable'])) {
				$body->setAttributes(array(
				    'class' => 'sortable',
				    'data-sort-href' => $this['sortable']->link('sortData!')
				));
			}
			if($this->hasEmptyData()) {
				$this->addRow($factory, $body, count($this->getColumns()), TRUE);
			} else {
				foreach ($full_data as $key => $rowData) {
					$this->addRow($factory, $body, $rowData);
					if(is_callable($this->sub_grid)) {
						$this->addRow($factory, $body, count($this->getColumns()), $sub_grids[$key]);
					}
				}
			}
			$table->setBody($body);
		}
		return $table;
	}

	private function addRow(&$factory, &$body, $rowData, $empty = FALSE) {
		$row = $factory->createRow($rowData);
		if(!$this->hasEmptyData()) {
			$row->setAttributes(array(
			    'id' => $this->getLineId($rowData)
			));
		}

		if($empty) {
			if($empty instanceof self) {
				$factory = new Render\Table\RendererFactory($empty);
				$table = $empty->createBody($factory);
				$empty_column = new Column\SubGrid(array(
				    Column\EmptyData::TEXT => $table->create()
				));
			} else {
				$empty_column = new Column\EmptyData(array(
				    Column\EmptyData::TEXT => $this->empty_text ? $this->empty_text : 'Nothing to display.'
				));
			}

			$cell = $factory->createCell($rowData, $empty_column);
			$row->addAttribute('class', 'no-sort ' . count($this->getColumns()));
			$row->addCell($cell);
		} else {
			foreach ($this->getColumns() as $column) {
				$row->addCell($factory->createCell($rowData, $column));
			}
		}

		$body->addRow($row);
		return $row;
	}

	private function rowsWalkRecursive(&$factory, &$row, $rowId, $groupData) {
		$sub_body = $factory->createBody();
		$sub_body->setAttributes(array(
		    'class' => 'grid-ul'
		));
		foreach ($groupData[$rowId] as $rowData) {
			$this->addTreeRow($factory, $sub_body, $rowData, $groupData);
		}
		$row->setBody($sub_body);
	}

	private function addTreeRow(&$factory, &$body, $rowData, $groupData) {
		$row = $this->addRow($factory, $body, $rowData);
		$sub_id = $rowData[$this->getDataSource()->getPrimaryKey()];
		if (isset($groupData[$sub_id])) {
			$this->rowsWalkRecursive($factory, $row, $sub_id, $groupData);
		}
	}
}

/**
 * Data grid exceptioin
 */
class Grid_Exception extends \Exception {

}