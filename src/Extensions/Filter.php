<?php

namespace DataGrid\Extensions;

use \Nette\Application\UI\Form,
    \Nette\Forms\Controls\SubmitButton,
    \Nette\Forms\Rendering\DefaultFormRenderer,
    \DataGrid\FilterFormRenderer,
	\DataGrid\Column\Date;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Filter extends BaseControl {

	/**
	 * @var Form
	 */
	private $filter_form;

	private $form_template;

	private $date_format = 'Y-m-d';

	private $js_date_format = 'YYYY-MM-DD';

	public function setDateFormat($format) {
		$this->date_format = $format;
		$this->js_date_format = Date::formatToMomentJsFormat($format);
	}

	public function setFilterForm(Form $filter_form, $template = NULL) {
		if (!isset($filter_form['reset']) || !isset($filter_form['filter'])) {
			throw new Grid_Exception('Filter form component have required submit buttons with names "reset" and "filter".');
		}
		if (!$filter_form['reset'] instanceof SubmitButton || !$filter_form['reset'] instanceof SubmitButton) {
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
		if (!$this->filter_form) {
			return NULL;
		}
		$this->fixSettingsForForm();
		$output = array();
		foreach($this->settings as $key => $val) {
			if(strpos($key, '[') !== FALSE) {
				$exploded = explode('[', $key);
				if(!isset($output[$exploded[0]])) {
					$output[$exploded[0]] = array();
				}
				$output[$exploded[0]][$exploded[1]] = $val;
			} else {
				$output[$key] = $val;
			}
		}
		return $output;
	}

	public function applyFilter() {
		if (!$this->filter_form) {
			$this->applyAutoFiltering();
		}
	}

	/**
	 * Create pager
	 */
	public function render() {
		$this->template->grid_dir = __DIR__;
		foreach($this->settings as $key => $val) {
			if(!in_array($key, $this->parent->getRealColumnNames())) {
				unset($this->settings[$key]);
			}
		}
		$this->template->settings = $this->settings;

		if (!$this->filter_form) {
			$this->template->php_date = $this->date_format;
			$this->template->js_date = $this->js_date_format;

			$this->template->setFile(dirname(__FILE__) . '/templates/Filter/Filter.latte');
		} else {
			$this->template->filter_form = $this->filter_form;
			$this->template->form_template = $this->form_template;

			$this->template->setFile(dirname(__FILE__) . '/templates/Filter/FilterForm.latte');
		}
		$this->template->render();
	}

	public function handleSubmitForm() {
		$this->fixSettingsForForm();
		if(isset($this->parent['pager'])) {
			$this->parent['pager']->reset(0);
		}
		$this->parent->onFilter($this->settings);
		$this->parent->redrawControl();
		$this->presenter->redrawControl();
	}

	private function fixSettingsForForm() {
		$values = $this->settings;
		if(isset($values['submittedBy'])) {
			$submittedBy = str_replace('_', '', $values['submittedBy']);
			unset($this->settings['submittedBy'], $this->getSession()->settings['submittedBy']);
			if($submittedBy === 'reset') {
				$this->settings = array();
				$this->getSession()->settings = array();
			}
		}
	}

	public function handleApplyDefaultFilter() {
		if(isset($this->parent['pager'])) {
			$this->parent['pager']->reset(0);
		}
		$this->parent->onFilter($this->settings);
		$this->parent->redrawControl();
		$this->presenter->redrawControl();
	}

	private function applyAutoFiltering() {
		foreach ($this->settings as $column_name => $values) {
			if (empty($values)) {
				continue;
			}
			foreach ($values as $key => $value) {
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