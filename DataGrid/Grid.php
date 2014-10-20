<?php
/**
 * Mesour Nette DataGrid
 *
 * Documentation here: http://grid.mesour.com
 *
 * @license LGPL-3.0 and BSD-3-Clause
 * @copyright (c) 2013 - 2014 Matous Nemec <matous.nemec@mesour.com>
 */

namespace DataGrid;

use Nette\Application\UI\Form,
    \Nette\ComponentModel\IContainer,
    \Nette\Application\UI\Control;

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
	private $page_limit = 20;

	/**
	 * Contains TRUE if before render called
	 *
	 * @var bool
	 */
	private $called_before_render = FALSE;

	/**
	 * Key for table row id
	 *
	 * @var string
	 */
	private $line_id_key;

	/**
	 * Value for table row id, it is key i result array
	 *
	 * @var string
	 */
	private $line_id_name;

	/**
	 * Data source
	 *
	 * @var \DataGrid\IDataSource
	 */
	private $data_source;

	/**
	 * Current count before apply limit
	 *
	 * @var integer
	 */
	private $count;

	private $real_column_names = NULL;

	/**
	 * Event which is triggered when sort data
	 *
	 * @var array
	 */
	public $onSort = array();

	/**
	 * Event which is triggered when edit column
	 *
	 * @var array
	 */
	public $onEditCell = array();

	/**
	 * Event which is triggered when filtering data
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

	/**
	 * Create data source instance
	 *
	 * @param \DataGrid\IDataSource $data_source Data source
	 * @param \Nette\ComponentModel\IContainer $parent
	 * @param string $name Name of data source
	 */
	public function __construct(IDataSource $data_source, IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);

		$this->data_source = $data_source;
		$this->name = $name;
		new Extensions\Ordering($this, 'ordering');
	}

	static public function disableJsDraw() {
		self::$js_draw = FALSE;
	}

	static public function disableCssDraw() {
		self::$css_draw = FALSE;
	}

	/**
	 * Get presenter name with data grid name
	 *
	 * @return String
	 */
	public function getGridName() {
		return $this->presenter->getName() . $this->name;
	}

	/**
	 * Add column to data grid
	 *
	 * @param Column\IColumn $column
	 */
	public function column(Column\IColumn $column) {
		$this->column_arr[] = $column;
	}

	/**
	 * Get column array
	 *
	 * @return Array
	 */
	public function getColumns() {
		return $this->column_arr;
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
		if(!is_null($filer_form)) {
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

	public function enablePager($max_for_normal = 15, $edge_page_count = 3, $middle_page_count = 2) {
		new Extensions\Pager($this, 'pager');
		$this['pager']->setMaxForNormal($max_for_normal)
		    ->setEdgePageCount($edge_page_count)
		    ->setMiddlePageCount($middle_page_count);
	}

	public function enableExport($cache_dir, array $columns = array()) {
		new Extensions\Export($this, 'export');
		$this['export']->setCacheDir($cache_dir);
		$this['export']->setColumns($columns);
	}

	public function enableRowSelection($primary_key, array $url_array, $show_main_checkbox = TRUE) {
		new Extensions\Selection($this, 'selection');
		$this['selection']->setPrimaryKey($primary_key);
		$this['selection']->setUrlArray($url_array);
		$this['selection']->setMainCheckboxShowing($show_main_checkbox);
	}

	public function enableSorting() {
		if (!$this->line_id_key) {
			throw new Grid_Exception('DataGrid sortable require line ID. Use setLineId.');
		}
		new Extensions\Sortable($this, 'sortable');
	}

	public function enableEditableCells() {
		if (!$this->line_id_key) {
			throw new Grid_Exception('DataGrid editable require line ID. Use setLineId.');
		}
		new Extensions\Editable($this, 'editable');
	}

	/**
	 * @param $selection_key
	 * @param array $url_array
	 * @param bool $checkbox_main
	 * @deprecated
	 */
	public function setCheckboxSelection($selection_key, array $url_array, $checkbox_main = TRUE) {
		$this->enableRowSelection($selection_key, $url_array, $checkbox_main);
	}

	/**
	 * Set sortable on data grid table
	 *
	 * @param callable $callback
	 * @throws Grid_Exception
	 * @deprecated
	 */
	public function sortable($callback) {
		$this->enableSorting();
		$this->onSort[] = $callback;
	}

	/**
	 * Enable editable columns
	 *
	 * @param callable $callback
	 * @throws Grid_Exception
	 * @deprecated
	 */
	public function editable($callback) {
		$this->enableEditableCells();
		$this->onEditCell[] = $callback;
	}

	/**
	 * @param $filter_form
	 * @param bool $auto_filtering
	 * @deprecated
	 */
	public function setFilterForm($filter_form, $auto_filtering = TRUE) {
		if($auto_filtering) {
			$this->enableFilter();
		} else {
			$this->enableFilter($this->presenter[$filter_form]);
		}
	}

	/**
	 * Set page limit
	 *
	 * @param integer $limit
	 */
	public function setPageLimit($limit) {
		$this->page_limit = $limit;
	}

	/**
	 * Get data source
	 *
	 * @return \DataGrid\IDataSource
	 */
	public function getDataSource() {
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

	/**
	 * Set line id. This will create id="name-{key}" on <tr> or <li>
	 * Required by sortable and editable
	 *
	 * @param String $key
	 * @param String $name
	 */
	public function setLineId($key, $name) {
		$this->line_id_key = $key;
		$this->line_id_name = $name;
	}

	public function getLineIdName() {
		return $this->line_id_name;
	}

	public function getLineId($data) {
		if ($this->hasLineId()) {
			return $this->line_id_name . '-' . $data[$this->line_id_key];
		}
		return FALSE;
	}

	public function fetchAll() {
		return $this->data_source->fetchAll();
	}

	public function getRealColumnNames() {
		if(is_null($this->real_column_names)) {
			$this->real_column_names = array_keys($this->data_source->fetch());
		}
		return $this->real_column_names;
	}

	public function hasEmptyData() {
		$column_names = $this->getRealColumnNames();
		return empty($column_names) ? TRUE : FALSE;
	}

	public function render() {
		$this->template->grid_dir = __DIR__;

		$factory = new Render\Table\RendererFactory($this);
		$table = $this->createBody($factory);
		$this->template->content = $table;

		$this->template->setFile(dirname(__FILE__) . '/Grid.latte');
		$this->template->render();
	}

	private function hasLineId() {
		return empty($this->line_id_key) === FALSE && empty($this->line_id_name) === FALSE;
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
			if($column instanceof Column\BaseOrdering && $this['ordering']->isDisabled()) {
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
		if ($this->called_before_render === TRUE) {
			return FALSE;
		}
		if (isset($this['filter'])) {
			$this['filter']->applyFilter();
		}
		$this->count = $this->data_source->count();

		if (isset($this['pager'])) {
			$this['pager']->setCounts($this->count, $this->page_limit);
			$this->data_source->applyLimit($this->page_limit, ($this['pager']->getCurrentPageIndex()) * $this->page_limit);
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
		$this->total_count = $this->data_source->getTotalCount();
	}

	protected function createBody(Render\IRendererFactory $factory) {
		$this->beforeCreate();
		$table = $factory->createTable();
		if($factory instanceof Render\Tree\RendererFactory) {
			$table_class = 'tree-grid';
		} else {
			$table_class = 'table table-striped table-condensed';
		}
		$table->setAttributes(array(
		    'class' => $table_class
		));
		$header = $factory->createHeader();
		$header->setAttributes(array('class' => 'grid-header'));
		foreach($this->getColumns() as $column) {
			$header->addCell($factory->createHeaderCell($column));
		}
		$table->setHeader($header);

		$this->beforeRender();
		if($factory instanceof Render\Tree\RendererFactory) {
			$data = $this->data_source->fetchAssoc();
			$body = $factory->createBody();
			$body_attributes = array(
			    'class' => 'grid-ul'
			);
			if(isset($this['sortable'])) {
				$body_attributes['class'] = 'grid-ul sortable';
				$body_attributes['data-sort-href'] = $this['sortable']->link('sortData!');
			}
			$body->setAttributes($body_attributes);
			foreach($data[0] as $rowData) {
				$this->addTreeRow($factory, $body, $rowData, $data);
			}
			$table->setBody($body);
		} else {
			$body = $factory->createBody();
			if(isset($this['sortable'])) {
				$body->setAttributes(array(
				    'class' => 'sortable',
				    'data-sort-href' => $this['sortable']->link('sortData!')
				));
			}
			foreach($this->data_source->fetchAll() as $rowData) {
				$this->addRow($factory, $body, $rowData);
			}
			$table->setBody($body);
		}
		return $table;
	}

	private function addRow(&$factory, &$body, $rowData) {
		$row = $factory->createRow($rowData, $this->getColumns());
		if($this->hasLineId()) {
			$row->setAttributes(array(
			    'id' => $this->getLineId($rowData)
			));
		}
		foreach($this->getColumns() as $column) {
			$row->addCell($factory->createCell($rowData, $column));
		}
		$body->addRow($row);
		return $row;
	}

	private function rowsWalkRecursive(&$factory, &$row, $rowId, $groupData) {
		$sub_body = $factory->createBody();
		$sub_body->setAttributes(array(
		    'class' => 'grid-ul'
		));
		foreach($groupData[$rowId] as $rowData) {
			$this->addTreeRow($factory, $sub_body, $rowData, $groupData);
		}
		$row->setBody($sub_body);
	}

	private function addTreeRow(&$factory, &$body, $rowData, $groupData) {
		$row = $this->addRow($factory, $body, $rowData);
		$sub_id = $rowData[$this->data_source->getPrimaryKey()];
		if(isset($groupData[$sub_id])) {
			$this->rowsWalkRecursive($factory, $row, $sub_id, $groupData);
		}
	}
}

/**
 * Data grid exceptioin
 */
class Grid_Exception extends \Exception {

}