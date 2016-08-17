<?php

namespace Mesour\DataGrid\Column;

use Mesour\DataGrid\Grid_Exception,
    Nette\Utils\Html;
use Nette\ComponentModel\IComponent;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Container extends BaseOrdering {

	/**
	 * Possible option key
	 */
	const COLUMNS = 'columns';

	protected function setDefaults() {
		return array(
		    self::COLUMNS => array()
		);
	}

	/**
	 * @param $column_name
	 * @param null|string $header
	 * @return Status
	 */
	public function addStatus($column_name, $header = NULL) {
		return $this->addColumn(new Status, $column_name, $header);
	}

	/**
	 * @param $column_name
	 * @param null|string $header
	 * @return Date
	 */
	public function addDate($column_name, $header = NULL) {
		return $this->addColumn(new Date, $column_name, $header);
	}

	/**
	 * @param $column_name
	 * @param null|string $header
	 * @return Number
	 */
	public function addNumber($column_name, $header = NULL) {
		return $this->addColumn(new Number, $column_name, $header);
	}

	/**
	 * @param $column_name
	 * @param null|string $header
	 * @return Text
	 */
	public function addText($column_name, $header = NULL) {
		return $this->addColumn(new Text, $column_name, $header);
	}

	/**
	 * @param $column_name
	 * @param null|string $header
	 * @return Image
	 */
	public function addImage($column_name, $header = NULL) {
		return $this->addColumn(new Image, $column_name, $header);
	}

	/**
	 * @param string $header
	 * @return Actions
	 */
	public function addActions($header) {
		return $this->addColumn(new Actions, NULL, $header);
	}

	/**
	 * @param $column_name
	 * @param null|string $header
	 * @return Template
	 */
	public function addTemplate($column_name, $header = NULL) {
		return $this->addColumn(new Template, $column_name, $header);
	}

	protected function addColumn(IColumn $column, $column_name = NULL, $header = NULL) {
		if (!is_null($header)) {
			$column->setHeader($header);
		}
		if (!is_null($column_name)) {
			$column->setId($column_name);
		}
		$this->option[self::COLUMNS][] = $column;
		return $column;
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		foreach($this->option[self::COLUMNS] as $column) {
			$column->setGridComponent($this->grid);
		}
		if (array_key_exists(self::HEADER, $this->option) === FALSE) {
			throw new Grid_Exception('Option ' . __CLASS__ . '::HEADER is required.');
		}
		if (array_key_exists(self::ID, $this->option) === FALSE) {
			$this->option[self::ORDERING] = FALSE;
		}
		return array(
		    'class' => 'grid-column-' . $this->option[self::ID]
		);
	}

	public function getHeaderContent() {
		return parent::getHeaderContent();
	}

	public function getBodyAttributes($data) {
		$attributes = array();
		$attributes['class'] = 'type-container';
		return $attributes;
	}

	public function getBodyContent($data, $export = FALSE) {
		$container = Html::el('span', array('class' => 'container-content'));
		$only_buttons = TRUE;
		foreach($this->option[self::COLUMNS] as $column) {
			if($export && isset($this->grid['export'])) {
				if(!$this->grid['export']->hasExport($column)) {
					continue;
				}
			}
			if($only_buttons && !$column instanceof Actions && !$column instanceof Status) {
				$only_buttons = FALSE;
			}
			$span = Html::el('span');
			$span->addAttributes($column->getHeaderAttributes());
			$span->addAttributes($column->getBodyAttributes($data));
			$content = $column->getBodyContent($data);
			if(!is_null($content)) {
				$span->addHtml($content);
			}
			$container->addHtml($span);
			$container->addHtml(' ');
		}
		if($only_buttons) {
			$container->class('only-buttons', TRUE);
		}
		return $export ? strip_tags($container) : $container;
	}

	public function hasExportableColumns() {
		$exportable = FALSE;
		foreach($this->option[self::COLUMNS] as $column) {
			if($this->grid['export']->hasExport($column)) {
				return TRUE;
			}
		}
		return $exportable;
	}

	/**
	 * @param IComponent $grid
	 * @return $this
	 */
	public function setGridComponent(IComponent $grid) {
		foreach($this->option[self::COLUMNS] as $column) {
			$column->setGridComponent($grid);
		}
		$this->grid = $grid;
		return $this;
	}

}