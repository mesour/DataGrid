<?php

namespace DataGrid;

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
	 * Sortable url
	 * 
	 * @var string
	 */
	protected $sortable;

	/**
	 * Added sortable data
	 * 
	 * @var array
	 */
	private $sort_data_arr = array();

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
	 * @param IColumn $column_option
	 */
	public function column(IColumn $column_option) {
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
	 * Pager will be show and pager URL
	 * 
	 * @param String $pager_url
	 * @deprecated Use enablePager() instead
	 */
	public function pagerUrl($pager_url) {
		$this->enablePager();
	}

	/**
	 * Pager will be show
	 */
	public function enablePager() {
		if($this->pager_enabled) {
			return;
		}
		$this->pager_enabled = TRUE;
		new Pager($this->getGridName(), $this, 'pager');
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
	 * @param String $filter_form
	 */
	public function setFilterForm($filter_form, $auto_filtering = TRUE, $like = TRUE) {
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
	 * @param String $sort_handler_link
	 * @param Array $sort_data_arr
	 */
	public function sortable($sort_handler_link, $sort_data_arr = array()) {
		$link = BaseColumn::checkLinkPermission($sort_handler_link);
		if ($link === FALSE) {
			return FALSE;
		}
		$this->sortable = $sort_handler_link;
		$this->sort_data_arr = $sort_data_arr;
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
		if(is_array($values) === FALSE) {
			$values = array();
		}
		$this->session_section->filter_values = $values;
		return $values;
	}
	
	/**
	 * Return filter
	 * 
	 * @return \Nette\Forms\UI\Form
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
		return $this->presenter->link($this->sortable, $this->sort_data_arr);
	}
	
	/**
	 * Return current line ID
	 * 
	 * @param Array $data Row data
	 * @return FALSE|String
	 */
	public function getLineId($data) {
		if($this->hasLineId()) {
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
				if ($component instanceof \Nette\Forms\Controls\SubmitButton){
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
			if($this->data_source instanceof \DataGrid\DibiDataSource) {
				if($this->auto_filter_like) {
					$this->data_source->where('%n', $key, ' LIKE %~like~', $value);
				} else {
					$this->data_source->where('%n', $key, ' = %s', $value);
				}
			} elseif($this->data_source instanceof \DataGrid\NetteDbDataSource) {
				if($this->auto_filter_like) {
					$this->data_source->where($key . ' LIKE ?', '%' . $value . '%');
				} else {
					$this->data_source->where($key . ' = ?', $value);
				}
			} elseif($this->data_source instanceof \DataGrid\ArrayDataSource) {
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
		if(isset($this->session_section->ordering) && !empty($this->session_section->ordering)) {
			foreach($this->session_section->ordering as $key => $how_to_order) {
				$this->data_source->orderBy($key, $how_to_order);
			}
		}
		if ($this->called_before_render === TRUE){
			return FALSE;
		}
		if (empty($this->filter_form) === FALSE){
			$this->applyFilter();
		}
		$this->count = $this->data_source->count();

		if ($this->pager_enabled) {
			$this['pager']->setCounts($this->getCount(), $this->getPageLimit());
			$this->data_source->applyLimit($this->page_limit, ($this['pager']->getCurrentPageIndex()) * $this->page_limit);
		}
		$this->called_before_render = TRUE;
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
		if($this->check_lang) {
			return $this->lang_checking;
		}
		return FALSE;
	}

	/**
	 * Set selection via checkboxes
	 * 
	 * @param String $selection_key
	 * @param Array $url_array
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
			$this->column_arr[-1] = new SelectionColumn($this->presenter, array(
			    SelectionColumn::ID => $this->selection_key,
			    SelectionColumn::CHECKBOX_MAIN => $this->checkbox_main,
			    SelectionColumn::CHECKBOX_ACTIONS => $this->selections
			));
		}
		if (empty($this->sortable) === FALSE) {
			$this->column_arr[-2] = new SortableColumn($this->presenter);
		}
		foreach($this->column_arr as $column) {
			$column->setGridName($this->name);
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
				$column = new TextColumn($this->presenter, array(
				    TextColumn::ID => $key,
				));
				$column->setGridName($this->name);
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
		$this->template->sortable = $this->sortable;
		$this->template->grid_dir = __DIR__;

		$this->template->setFile( dirname( __FILE__ ) . '/templates/DataGrid.latte' );
                $this->template->render();
	}

	public function getOrdering($column_id) {
		if(!isset($this->session_section->ordering[$column_id])) {
			return NULL;
		} else {
			return $this->session_section->ordering[$column_id];
		}
	}

	public function handleOrdering($column_id) {
		if(!isset($this->session_section->ordering)) {
			$this->session_section->ordering = array();
		}
		if(!isset($this->session_section->ordering[$column_id])) {
			$this->session_section->ordering[$column_id] = 'ASC';
		} elseif($this->session_section->ordering[$column_id] === 'ASC') {
			$this->session_section->ordering[$column_id] = 'DESC';
		} else {
			unset($this->session_section->ordering[$column_id]);
		}
		$this->redrawControl();
		$this->presenter->redrawControl();
	}
	
	//protected function createComponentPager($name) {
	//	return new \DataGrid\Pager($this, $name);
	//}
}

/**
 * Data grid exceptioin
 */
class Grid_Exception extends \Exception {
	
}