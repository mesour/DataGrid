<?php
/**
 * Mesour Nette BaseDataGrid
 *
 * Documentation here: http://grid.mesour.com
 *
 * @license LGPL-3.0 and BSD-3-Clause
 * @copyright (c) 2013 - 2015 Matous Nemec <matous.nemec@mesour.com>
 */

namespace Mesour\DataGrid;

use Mesour\DataGrid\Column\IColumn;
use Nette\ComponentModel\IContainer,
    Nette\Application\UI\Control,
    Mesour\DataGrid\Render\IRendererFactory,
    Mesour\DataGrid\Render\Renderer;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 *
 * @property-read IRendererFactory $rendererFactory
 * @property-read IDataSource $dataSource
 */
abstract class BaseGrid extends Control {

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
	protected $column_arr = array();

	/**
	 * Total count of result
	 *
	 * @var int|FALSE
	 */
	private $total_count = FALSE;

	/**
	 * @var IRendererFactory
	 */
	private $rendererFactory;

	/**
	 * Data source
	 *
	 * @var \Mesour\DataGrid\IDataSource
	 */
	private $dataSource;

	/**
	 * Current count before apply limit
	 *
	 * @var integer
	 */
	private $count;

	private $real_column_names = NULL;

	/**
	 * @var bool
	 */
	static public $js_draw = TRUE;

	/**
	 * @var bool
	 */
	static public $css_draw = TRUE;

	private $primary_key = NULL;

	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		$this->name = $name;
		new Extensions\Translator($this, 'translator');
		$this["translator"]->setLocale("en");
	}

	public function setName($name) {
		$this->name = $name;
		$this->grid_name = NULL;
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
		if(!$this->dataSource) {
			throw new Grid_Exception('Data source is not set. Use setDataSource.');
		}
		return $this->dataSource;
	}

	/**
	 * Get count without where and limit
	 *
	 * @return Integer
	 */
	public function getTotalCount() {
		if ($this->total_count === FALSE) {
			$this->setTotalCount();
		}
		return $this->total_count;
	}

	/**
	 * @param $languageFile - Set language file
	 * @param null $customDir - Set custom directory (directory where you have translates from grid)
	 * @throws Grid_Exception
	 */
	public function setLocale($languageFile, $customDir = null) {
		$this["translator"]->setLocale($languageFile, $customDir);
	}

	public function fetchAll() {
		return $this->getDataSource()->fetchAll();
	}

	public function getRealColumnNames($full_data = array()) {
		if (is_null($this->real_column_names)) {
			if(!empty($full_data)) {
				$x = (array) reset($full_data);
				$this->real_column_names = array_keys($x);
			} else {
				$this->real_column_names = array_keys($this->getDataSource()->fetch());
			}
		}
		return $this->real_column_names;
	}

	public function hasEmptyData($full_data = array()) {
		$column_names = $this->getRealColumnNames($full_data);
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

	public function setDataSource(IDataSource & $dataSource) {
		$this->dataSource = $dataSource;
		if(is_null($this->primary_key)) {
			$this->primary_key = $this->dataSource->getPrimaryKey();
		} else {
			$this->dataSource->setPrimaryKey($this->primary_key);
		}
	}

	public function setPrimaryKey($primary_key) {
		$this->primary_key = $primary_key;
		if($this->dataSource) {
			$this->dataSource->setPrimaryKey($primary_key);
		}
	}

	public function getPrimaryKey() {
		return $this->primary_key;
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

	public function getRendererFactory() {
		return $this->rendererFactory;
	}

	public function setRendererFactory(IRendererFactory $rendererFactory) {
		$this->rendererFactory = $rendererFactory;
	}

	abstract public function render();

	protected function getLineId($data) {
		if(!isset($data[$this->getPrimaryKey()])) {
			throw new Grid_Exception('Primary key "' . $this->getPrimaryKey() . '" does not exists in data. For change use setPrimaryKey on DataSource.');
		}
		return $this->getName() . '-' . $data[$this->getPrimaryKey()];
	}

	/**
	 * Must called before create body
	 */
	protected function beforeCreate() {
		foreach ($this->column_arr as $column) {
			$column->setGridComponent($this);
			if ($column instanceof Column\BaseOrdering && $this['ordering']->isDisabled()) {
				$column->setOrdering(FALSE);
			}
		}
		$this->checkEmptyColumns();
	}

	/**
	 * Must called before render header
	 */
	protected function beforeRender() {
		$this->template->locale = $this["translator"]->getLocale();
	}

	protected function updateCounts() {
		return $this->count = $this->getDataSource()->count();
	}

	/**
	 * Check if columns are empty, if empty, set default Text columns by DB
	 */
	protected function checkEmptyColumns() {
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
	 * @param IColumn $column
	 * @return IColumn
	 */
	protected function addColumn(IColumn $column) {
		$this->column_arr[] = $column;
		return $column;
	}

	/**
	 * Set total count of data grid
	 */
	private function setTotalCount() {
		$this->total_count = $this->getDataSource()->getTotalCount();
	}

	/**
	 * @param string $table_class
	 * @return Renderer
	 * @throws Grid_Exception
	 */
	public function createBody($table_class = 'table') {
		if(!$this->rendererFactory) {
			throw new Grid_Exception('RendererFactory is not set.');
		}
		$this->beforeCreate();
		$table = $this->rendererFactory->createTable();
		$table->setAttributes(array(
		    'class' => $table_class
		));
		$this->beforeRender();
		return $table;
	}
}