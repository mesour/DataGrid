<?php

namespace Mesour\DataGrid\Extensions;

use Mesour\DataGrid\Grid_Exception;
use \Nette\Application\UI\Form,
    \Nette\Forms\Controls\SubmitButton,
    \Nette\Forms\Rendering\DefaultFormRenderer,
    Mesour\DataGrid\FilterFormRenderer,
    Mesour\DataGrid\Column\Date;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Filter extends BaseControl {

	/**
	 * @var Form
	 */
	private $filter_form;

	private $filter_values = NULL;

	private $form_template;

	private $date_format = 'Y-m-d';

	private $js_date_format = 'YYYY-MM-DD';

	/**
	 * @var array
	 * @persistent
	 */
	public $dropdown = array();

	public function setDateFormat($format) {
		$this->date_format = $format;
		$this->js_date_format = Date::formatToMomentJsFormat($format);
	}

	public function setFilterForm(Form $filter_form, $template = NULL) {
		if (!isset($filter_form['reset']) || !isset($filter_form['filter'])) {
			throw new Grid_Exception('Filter form component have required submit buttons with names "reset" and "filter".');
		}
		if (!$filter_form['reset'] instanceof SubmitButton || !$filter_form['filter'] instanceof SubmitButton) {
			throw new Grid_Exception('Filter form\'s components "reset" and "filter" must be instanceof \Nette\Forms\Controls\SubmitButton.');
		}
		if (is_null($template) && $filter_form->getRenderer() instanceof DefaultFormRenderer) {
			$filter_form->setRenderer(new FilterFormRenderer);
		}
		$filter_form->getElementPrototype()
		    ->class('form-inline', TRUE)
		    ->role('form')
		    ->action($this->link('submitForm!'));
		$this->filter_form = $filter_form;
		$this->form_template = $template;
	}

	/**
	 * Get filter values for manual filtering
	 * If filter form is not set return NULL
	 *
	 * @return NULL|Array
	 */
	public function getFilterValues() {
		if (!$this->isAdvanced()) {
			return NULL;
		}
		$this->fixSettingsForForm();
		$output = array();
		foreach ($this->settings as $key => $val) {
			if (strpos($key, '[') !== FALSE) {
				$exploded = explode('[', $key);
				if (!isset($output[$exploded[0]])) {
					$output[$exploded[0]] = array();
				}
				$output[$exploded[0]][$exploded[1]] = $val;
			} else {
				$output[$key] = $val;
			}
		}
		$this->filter_values = $output;
		return $output;
	}

	public function applyFilter() {
		if (!$this->filter_form) {
			$this->applyAutoFiltering();
		}
		$this->getFilterValues();
	}

	/**
	 * Create pager
	 */
	public function render() {
		$this->template->grid_dir = __DIR__;
		foreach ($this->settings as $key => $val) {
			if (!in_array($key, $this->parent->getRealColumnNames())) {
				unset($this->settings[$key]);
			}
		}

		if (!$this->isAdvanced()) {
			$this->template->php_date = $this->date_format;
			$this->template->js_date = $this->js_date_format;
			$this->template->settings = $this->settings;

			if(!isset($this->getSession()->dropdown)) {
				$this->getSession()->dropdown = FALSE;
			}

			$this->template->setFile(dirname(__FILE__) . '/templates/Filter/Filter.latte');
		} else {
			$this->template->filter_form = $this->filter_form;
			$this->template->form_template = $this->form_template;

			$this->template->settings = is_null($this->filter_values) ? $this->getFilterValues() : $this->filter_values;

			$this->template->setFile(dirname(__FILE__) . '/templates/Filter/FilterForm.latte');
		}
		$this->template->render();
	}

	public function isAdvanced() {
		return !$this->filter_form ? FALSE : TRUE;
	}

	public function handleSubmitForm() {
		$this->fixSettingsForForm();
		$this->parent->reset();
		$this->parent->onFilter($this->settings);
		$this->parent->redrawControl();
		$this->presenter->redrawControl();
	}

	private function fixSettingsForForm() {
		$values = $this->settings;
		if (isset($values['submittedBy'])) {
			$submittedBy = str_replace('_', '', $values['submittedBy']);
			unset($this->settings['submittedBy'], $this->getSession()->settings['submittedBy']);
			if ($submittedBy === 'reset') {
				$this->settings = array();
				$this->getSession()->settings = array();
			}
		}
	}

	public function handleApplyDefaultFilter() {
		$this->parent->reset();
		$this->parent->onFilter($this->settings);
		$this->parent->redrawControl();
		$this->presenter->redrawControl();
	}

	private function applyAutoFiltering() {
		$realColumnNames = $this->parent->getRealColumnNamesForFilter();
		foreach ($this->settings as $column_name => $values) {
			if (empty($values)) {
				continue;
			}
			$column_name = isset($realColumnNames[$column_name]) ? $realColumnNames[$column_name] : $column_name;
			foreach ($values as $key => $value) {
				$value = is_numeric($value) ? (float) $value : $value;
				switch ($key) {
					case 'priority':
						continue;
					case 'checkers':
						$this->parent->getDataSource()->applyCheckers($column_name, $value, $values['type']);
						break;
					case 'custom':
						$this->parent->getDataSource()->applyCustom($column_name, $value, $values['type']);
						break;
				}
			}
		}
	}

}
