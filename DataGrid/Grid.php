<?php

namespace DataGrid;

use DataGrid\Render\Table\Renderer;
use Nette\Application\UI\Form,
    \Nette\Forms\Controls\SubmitButton;

/**
 * Description of \DataGrid\Grid
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class Grid extends \Nette\Application\UI\Control {

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
	 * @var int
	 */
	private $total_count = 0;

	/**
	 * True if total count was set
	 *
	 * @var bool
	 */
	private $total_count_set = FALSE;

	/**
	 * Limit for one page
	 *
	 * @var ing
	 */
	private $page_limit = 20;

	/**
	 * Contains TRUE if before render called
	 *
	 * @var bool
	 */
	private $called_before_render = FALSE;

	/**
	 * Sortable callback
	 *
	 * @var string
	 */
	protected $sortable_callback;

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
	 *
	 * @var bool
	 */
	private $check_lang = FALSE;

	/**
	 * Lang checking option
	 *
	 * @var array
	 */
	private $lang_checking = array();

	/**
	 * Data source
	 *
	 * @var \DataGrid\IDataSource
	 */
	private $data_source;

	/**
	 * Checkbox selection
	 *
	 * @var array
	 */
	protected $selections = array();

	/**
	 * Checkbox selection key
	 *
	 * @var string
	 */
	private $selection_key;

	/**
	 * For selections
	 *
	 * @var bool
	 */
	private $checkbox_main = TRUE;

	/**
	 * Name of filter form
	 *
	 * @var string
	 */
	protected $filter_form;

	/**
	 * Contains filter form
	 *
	 * @var \Nette\Forms\Form
	 */
	private $filter;

	/**
	 * Auto filtering
	 *
	 * @var bool
	 */
	private $auto_filtering = TRUE;

	/**
	 * Using SQL like operator in filtering
	 *
	 * @var bool
	 */
	private $auto_filter_like = TRUE;

	/**
	 * Current count before apply limit
	 *
	 * @var integer
	 */
	private $count;

	/**
	 * Private grid session
	 *
	 * @var \Nette\Http\SessionSection
	 */
	private $session_section;

	/**
	 * Enbled pager
	 *
	 * @var bool
	 */
	private $pager_enabled = FALSE;

	/**
	 * Callback for editable columns
	 *
	 * @var String
	 */
	private $editable_callback;

	/**
	 * Http request
	 *
	 * @inject \Nette\Http\IRequest
	 */
	private $http_request = FALSE;

	/**
	 * Contains true if export is enabled
	 *
	 * @var bool
	 */
	private $export = FALSE;

	/**
	 * Restriction for some columns
	 *
	 * @var array
	 */
	private $export_columns = array();

	/**
	 * Cache directory
	 *
	 * @var string
	 */
	private $cache_dir;

	/**
	 * Create data source instance
	 *
	 * @param \DataGrid\IDataSource $data_source Data source
	 * @param \Nette\ComponentModel\IContainer $parent
	 * @param string $name Name of data source
	 */
	public function __construct(\DataGrid\IDataSource $data_source, \Nette\ComponentModel\IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);

		$this->data_source = $data_source;
		$this->name = $name;
		$this->session_section = $this->presenter->getSession()->getSection('dataGrid_' . $this->getGridName());
	}

	public function injectHttpRequest(\Nette\Http\IRequest $http_request) {
		$this->http_request = $http_request;
	}

	public function getHttpRequest() {
		return $this->http_request;
	}

	/**
	 * Get presenter name with datagrid name
	 *
	 * @return String
	 */
	public function getGridName() {
		return $this->presenter->getName() . $this->name;
	}

	/**
	 * Add data grid column
	 *
	 * @param Column\IColumn $column_option
	 */
	public function column(Column\IColumn $column_option) {
		$this->column_arr[] = $column_option;
	}

	/**
	 * Get column array
	 *
	 * @return Array
	 */
	public function getColumns() {
		return $this->column_arr;
	}

	/**
	 * Pager will be show
	 */
	public function enablePager($max_for_normal = 15, $edge_page_count = 3, $middle_page_count = 2) {
		if ($this->pager_enabled) {
			return;
		}
		if ($this->http_request === FALSE) {
			throw new Grid_Exception('DataGrid pager require HTTP request. Use injectHttpRequest.');
		}
		$this->pager_enabled = TRUE;
		new Pager($this->getGridName(), $this, 'pager');
		$this['pager']->setMaxForNormal($max_for_normal)
			->setEdgePageCount($edge_page_count)
			->setMiddlePageCount($middle_page_count);
	}

	public function setCacheDir($dir) {
		if (!is_dir($dir)) {
			throw new Grid_Exception('Cache dir is not a directory.');
		}
		if (!is_writable($dir)) {
			throw new Grid_Exception('Cache dir is not a writable.');
		}
		$this->cache_dir = $dir;
	}

	public function enableExport(array $columns = array()) {
		if (!$this->cache_dir) {
			throw new Grid_Exception('Export required cache dir. Use setCacheDir.');
		}
		$this->export = TRUE;
		$this->export_columns = $columns;
	}

	public function isExportEnabled() {
		return $this->export;
	}

	public function getExportLink() {
		return $this->link('export!');
	}

	public function handleExport() {
		$header_arr = array();

		if ($this->filter_form && $this->auto_filtering) {
			$this->applyAutoFiltering();
		}

		$file_name = $this->cache_dir . "/" . $this->getGridName() . time() . ".csv";
		$file = fopen($file_name, "w");
		foreach ($this->column_arr as $column) {
			if ($column instanceof Column\Text || $column instanceof Column\Number || $column instanceof Column\Date) {
				if (empty($this->export_columns) || in_array($column->getId(), $this->export_columns)) {
					$header_arr[] = $column->getText();
				}
			}
		}
		fputcsv($file, $header_arr);
		foreach ($this->data_source->fetchAllForExport() as $data) {
			$line_data = array();
			foreach ($this->column_arr as $column) {
				if ($column instanceof Column\Text || $column instanceof Column\Number || $column instanceof Column\Date) {
					if (empty($this->export_columns) || in_array($column->getId(), $this->export_columns)) {
						$line_data[] = $data[$column->getId()];
					}
				}
			}
			fputcsv($file, $line_data);
		}
		fclose($file);

		echo file_get_contents($file_name);
		unlink($file_name);

		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $this->getGridName() . '.csv"');
		header('Content-Transfer-Encoding: binary');
		header('Connection: Keep-Alive');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		exit;
	}

	/**
	 * Enable editable columns
	 *
	 * @param callable $callback
	 * @throws Grid_Exception
	 */
	public function editable(callable $callback) {
		if (!$this->line_id_key) {
			throw new Grid_Exception('DataGrid editable require line ID. Use setLineId.');
		}
		if ($this->http_request === FALSE) {
			throw new Grid_Exception('DataGrid editable require HTTP request. Use injectHttpRequest.');
		}
		$this->editable_callback = $callback;
	}

	/**
	 * Get editable link
	 *
	 * @return string
	 */
	public function getEditableLink() {
		return $this->link('editable!');
	}

	/**
	 * Check if DataGrid is editable
	 *
	 * @return bool
	 */
	public function isEditable() {
		return (bool)$this->editable_callback;
	}

	/**
	 * @throws Grid_Exception
	 */
	public function handleEditable() {
		$data = $this->http_request->getPost('data');
		if (!is_array($data)) {
			throw new Grid_Exception('Empty post data from column edit.');
		}
		$has_permission = FALSE;
		foreach ($this->column_arr as $column) {
			if ($column->getId() === $data['columnName'] && $column->isEditable()) {
				$has_permission = TRUE;
			}
		}
		if ($has_permission) {
			call_user_func_array($this->editable_callback, array($data['lineId'], $data['columnName'], $data['newValue'], $data['oldValue']));
		} else {
			throw new Grid_Exception('Column with ID ' . $data['columnName'] . ' is not editable or does not exists in DataGrid columns.');
		}
	}

	/**
	 * Returns true if pager is enabled
	 *
	 * @return bool
	 */
	public function isPagerEnabled() {
		return $this->pager_enabled;
	}

	/**
	 * Set name of filter form
	 *
	 * @param String $filter_form Form component name
	 * @param bool $auto_filtering
	 * @param bool $like
	 * @throws Grid_Exception
	 */
	public function setFilterForm($filter_form, $auto_filtering = TRUE, $like = TRUE) {
		$component = $this->presenter->getComponent($filter_form);
		if (!$component instanceof Form) {
			throw new Grid_Exception('Filter form component must be instanceof Nette\Application\UI\Form.');
		}
		if (!isset($component['reset']) || !isset($component['filter'])) {
			throw new Grid_Exception('Filter form component have required submit buttons with names "reset" and "filter".');
		}
		if (!$component['reset'] instanceof SubmitButton || !$component['reset'] instanceof SubmitButton) {
			throw new Grid_Exception('Filter form\'s components "reset" and "filter" must be instanceof \Nette\Forms\Controls\SubmitButton.');
		}
		$this->filter_form = $filter_form;
		$this->auto_filtering = $auto_filtering;
		$this->auto_filter_like = $like;

		$this->filter = $this->presenter->getComponent($this->filter_form);
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
		if (!$this->total_count_set) {
			$this->setTotalCount();
		}
		return $this->total_count;
	}

	/**
	 * Get current count without limit
	 *
	 * @return integer
	 */
	public function getCount() {
		return $this->count;
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
	 * Get current page limit
	 *
	 * @return integer
	 */
	public function getPageLimit() {
		return $this->page_limit;
	}

	/**
	 * Set sortable on data grid table
	 *
	 * @param callable $callback
	 * @throws Grid_Exception
	 */
	public function sortable(callable $callback) {
		if (!$this->line_id_key) {
			throw new Grid_Exception('DataGrid sortable require line ID. Use setLineId.');
		}
		if ($this->http_request === FALSE) {
			throw new Grid_Exception('DataGrid editable require HTTP request. Use injectHttpRequest.');
		}
		$this->sortable_callback = $callback;
	}

	/**
	 * @throws Grid_Exception
	 */
	public function handleSortable() {
		$data = $this->http_request->getPost($this->line_id_name);
		if (!is_array($data)) {
			throw new Grid_Exception('Empty post data from column sorting.');
		}
		call_user_func($this->sortable_callback, $data);
	}

	/**
	 * Check if DataGrid is sortable
	 *
	 * @return bool
	 */
	public function isSortable() {
		return (bool)$this->sortable_callback;
	}

	/**
	 * Set line id for example: for sorting
	 *
	 * @param String $line_id_key
	 * @param String $line_id_name
	 */
	public function setLineId($line_id_key, $line_id_name) {
		$this->line_id_key = $line_id_key;
		$this->line_id_name = $line_id_name;
	}

	/**
	 * Get filter values for manual filtering
	 *
	 * @return Array
	 */
	public function getFilterValues() {
		if ($this->filter['reset']->isSubmittedBy()) {
			$values = array();
		} elseif ($this->filter['filter']->isSubmittedBy()) {
			$values = $this->filter->getValues(TRUE);
		} else {
			$values = $this->session_section->filter_values;
		}
		if (is_array($values) === FALSE) {
			$values = array();
		}
		$this->session_section->filter_values = $values;
		return $values;
	}

	/**
	 * Return filter
	 *
	 * @return \Nette\Application\UI\Form
	 */
	public function getFilter() {
		return $this->filter;
	}

	/**
	 * Return sortable link
	 *
	 * @return String
	 */
	public function getSortableLink() {
		return $this->link('sortable!');
	}

	/**
	 * Return current line ID
	 *
	 * @param Array $data Row data
	 * @return FALSE|String
	 */
	public function getLineId($data) {
		if ($this->hasLineId()) {
			return $this->line_id_name . '-' . $data[$this->line_id_key];
		}
		return FALSE;
	}

	/**
	 * Check if grid have active lang checking
	 *
	 * @param Array $data Row data
	 * @return Bool
	 */
	public function hasLangChecking($data) {
		return $this->check_lang === TRUE && is_null($data[$this->lang_checking['column']]) === TRUE;
	}

	/**
	 * Check if grid have line ID
	 *
	 * @return Bool
	 */
	public function hasLineId() {
		return empty($this->line_id_key) === FALSE && empty($this->line_id_name) === FALSE;
	}

	/**
	 * Apply filter to data source
	 */
	public function applyFilter() {
		if ($this->filter['reset']->isSubmittedBy()) {
			foreach ($this->filter->getComponents() as $name => $component) {
				if ($component instanceof \Nette\Forms\Controls\SubmitButton) {
					continue;
				}
				$this->filter[$name]->setValue(NULL);
			}
		} elseif ($this->filter['filter']->isSubmittedBy()) {

		} else {
			$this->filter->setValues($this->getFilterValues());
		}

		if ($this->auto_filtering) {
			$this->applyAutoFiltering();
		}
	}

	/**
	 * Apply auto filtering
	 *
	 * @throws \DataGrid\Grid_Exception
	 */
	private function applyAutoFiltering() {
		foreach ($this->getFilterValues() as $key => $value) {
			if (empty($value))
				continue;
			if ($this->data_source instanceof \DataGrid\DibiDataSource) {
				if ($this->auto_filter_like) {
					$this->data_source->where('%n', $key, ' LIKE %~like~', $value);
				} else {
					$this->data_source->where('%n', $key, ' = %s', $value);
				}
			} elseif ($this->data_source instanceof \DataGrid\NetteDbDataSource) {
				if ($this->auto_filter_like) {
					$this->data_source->where($key . ' LIKE ?', '%' . $value . '%');
				} else {
					$this->data_source->where($key . ' = ?', $value);
				}
			} elseif ($this->data_source instanceof \DataGrid\ArrayDataSource) {
				$this->data_source->where($key, $value);
			} else {
				throw new \DataGrid\Grid_Exception('Not supported data source type for filtering.');
			}
		}
	}

	/**
	 * Must called before render header
	 */
	public function beforeRender() {
		$this->ordering();
		if ($this->called_before_render === TRUE) {
			return FALSE;
		}
		if (empty($this->filter_form) === FALSE) {
			$this->applyFilter();
		}
		$this->count = $this->data_source->count();

		if ($this->pager_enabled) {
			$this['pager']->setCounts($this->getCount(), $this->getPageLimit());
			$this->data_source->applyLimit($this->page_limit, ($this['pager']->getCurrentPageIndex()) * $this->page_limit);
		}
		$this->called_before_render = TRUE;
	}

	private function ordering() {
		if (isset($this->session_section->ordering) && !empty($this->session_section->ordering)) {
			$data = array_keys($this->data_source->fetch());
			foreach ($this->session_section->ordering as $key => $value) {
				if (!in_array($key, $data)) {
					unset($this->session_section->ordering[$key]);
				}
			}

			foreach ($this->session_section->ordering as $key => $how_to_order) {
				$this->data_source->orderBy($key, $how_to_order);
			}
		}
	}

	/**
	 * Set lang checking for unfounded items
	 *
	 * @param String $description
	 * @param Array $button_option
	 * @param String $title
	 * @param String $column
	 * @param String $parent_column
	 */
	public function setLangChecking($description, array $button_option, $title = 'Unfounded', $column = 'name', $parent_column = 'private_name') {
		$this->check_lang = TRUE;
		$this->lang_checking['description'] = $description;
		$this->lang_checking['button_option'] = $button_option;
		$this->lang_checking['title'] = $title;
		$this->lang_checking['column'] = $column;
		$this->lang_checking['parent_column'] = $parent_column;
	}

	/**
	 * Get lang checking array
	 *
	 * @return FALSE|Array
	 */
	public function getLangChecking() {
		if ($this->check_lang) {
			return $this->lang_checking;
		}
		return FALSE;
	}

	/**
	 * Set selection via checkboxes
	 *
	 * @param $selection_key
	 * @param array $url_array
	 * @param bool $checkbox_main
	 */
	public function setCheckboxSelection($selection_key, array $url_array, $checkbox_main = TRUE) {
		$this->selection_key = $selection_key;
		$this->selections = $url_array;
		$this->checkbox_main = $checkbox_main;
	}

	/**
	 * Fetch and get all results from data source
	 *
	 * @return Array
	 */
	public function fetchAll() {
		return $this->data_source->fetchAll();
	}

	/**
	 * Must called before create body
	 */
	public function beforeCreate() {
		if (empty($this->selections) === FALSE) {
			$this->column_arr[-1] = new Column\Selection(array(
			    Column\Selection::ID => $this->selection_key,
			    Column\Selection::CHECKBOX_MAIN => $this->checkbox_main,
			    Column\Selection::CHECKBOX_ACTIONS => $this->selections
			));
		}
		if (empty($this->sortable_callback) === FALSE) {
			$this->column_arr[-2] = new Column\Sortable();
		}
		foreach ($this->column_arr as $column) {
			$column->setGridComponent($this);
		}
		ksort($this->column_arr);
		$this->checkEmptyColumns();
	}

	/**
	 * Check if empty columns
	 */
	private function checkEmptyColumns() {
		if (empty($this->column_arr)) {
			foreach (array_keys($this->data_source->fetch()) as $key) {
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
		$this->total_count_set = TRUE;
		$this->total_count = $this->data_source->getTotalCount();
	}

	/**
	 * Render control
	 */
	public function render() {
		$this->template->filter_form = $this->filter_form;
		$this->template->selections = $this->selections;
		$this->template->grid_dir = __DIR__;

		$factory = new Render\Table\RendererFactory($this);
		$table = $this->createBody($factory);
		$this->template->content = $table;

		$this->template->setFile(dirname(__FILE__) . '/templates/Grid.latte');
		$this->template->render();
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
			if($this->isSortable()) {
				$body_attributes['class'] = 'grid-ul sortable';
				$body_attributes['data-sort-href'] = $this->getSortableLink();
			}
			$body->setAttributes($body_attributes);
			foreach($data[0] as $rowData) {
				$this->addTreeRow($factory, $body, $rowData, $data);
			}
			$table->setBody($body);
		} else {
			$body = $factory->createBody();
			if($this->isSortable()) {
				$body->setAttributes(array(
				    'class' => 'sortable',
				    'data-sort-href' => $this->getSortableLink()
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

	/**
	 * Ged ordering for column
	 *
	 * @param $column_id
	 * @return null
	 */
	public function getOrdering($column_id) {
		if (!isset($this->session_section->ordering) || !isset($this->session_section->ordering[$column_id])) {
			return NULL;
		} else {
			return $this->session_section->ordering[$column_id];
		}
	}

	/**
	 * @param $column_id
	 */
	public function handleOrdering($column_id) {
		if (!isset($this->session_section->ordering)) {
			$this->session_section->ordering = array();
		}
		if (!isset($this->session_section->ordering[$column_id])) {
			$this->session_section->ordering[$column_id] = 'ASC';
		} elseif ($this->session_section->ordering[$column_id] === 'ASC') {
			$this->session_section->ordering[$column_id] = 'DESC';
		} else {
			unset($this->session_section->ordering[$column_id]);
		}

		$this->redrawControl();
		$this->presenter->redrawControl();
	}
}

/**
 * Data grid exceptioin
 */
class Grid_Exception extends \Exception {

}