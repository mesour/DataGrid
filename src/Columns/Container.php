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

	public function addStatus($column_name, $header = NULL) {
		$column = new Status();
		$column->setId($column_name)
		    ->setHeader($header);
		$this->option[self::COLUMNS][] = $column;
		return $column;
	}

	public function addDate($column_name, $header = NULL) {
		$column = new Date();
		$column->setId($column_name)
		    ->setHeader($header);
		$this->option[self::COLUMNS][] = $column;
		return $column;
	}

	public function addNumber($column_name, $header = NULL) {
		$column = new Number();
		$column->setId($column_name)
		    ->setHeader($header);
		$this->option[self::COLUMNS][] = $column;
		return $column;
	}

	public function addText($column_name, $header = NULL) {
		$column = new Text();
		$column->setId($column_name)
		    ->setHeader($header);
		$this->option[self::COLUMNS][] = $column;
		return $column;
	}

	public function addImage($column_name, $header = NULL) {
		$column = new Image();
		$column->setId($column_name)
		    ->setHeader($header);
		$this->option[self::COLUMNS][] = $column;
		return $column;
	}

	public function addActions($header = NULL) {
		$column = new Actions();
		$column->setHeader($header);
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
				$span->add($content);
			}
			$container->add($span);
			$container->add(' ');
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