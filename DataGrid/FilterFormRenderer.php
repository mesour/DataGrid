<?php

namespace DataGrid;


/**
 * Description of filter form renderer for data grid
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class FilterFormRenderer extends \Nette\Forms\Rendering\DefaultFormRenderer
{
	/**
	 *  /--- form.container
	 *
	 *    /--- if (form.errors) error.container
	 *      .... error.item [.class]
	 *    \---
	 *
	 *    /--- hidden.container
	 *      .... HIDDEN CONTROLS
	 *    \---
	 *
	 *    /--- group.container
	 *      .... group.label
	 *      .... group.description
	 *
	 *      /--- controls.container
	 *
	 *        /--- pair.container [.required .optional .odd]
	 *
	 *          /--- label.container
	 *            .... LABEL
	 *            .... label.suffix
	 *            .... label.requiredsuffix
	 *          \---
	 *
	 *          /--- control.container [.odd]
	 *            .... CONTROL [.required .text .password .file .submit .button]
	 *            .... control.requiredsuffix
	 *            .... control.description
	 *            .... if (control.errors) error.container
	 *          \---
	 *        \---
	 *      \---
	 *    \---
	 *  \--
	 *
	 * @var array of HTML tags */
	public $wrappers = array(
		'form' => array(
			'container' => NULL,
			'errors' => TRUE,
		),

		'error' => array(
			'container' => 'ul class=error',
			'item' => 'li',
		),

		'group' => array(
			'container' => 'fieldset',
			'label' => 'legend',
			'description' => 'p',
		),

		'controls' => array(
			'container' => NULL,
		),

		'pair' => array(
			'container' => 'div class=form-group',
			'.required' => 'required',
			'.optional' => NULL,
			'.odd' => NULL,
		),

		'control' => array(
			'container' => NULL,
			'.odd' => NULL,

			'errors' => FALSE,
			'description' => 'small',
			'requiredsuffix' => '',

			'.required' => 'required',
			'.text' => 'form-control',
			'.password' => 'form-control',
			'.file' => 'form-control',
			'.textarea' => 'form-control',
			'.submit' => 'btn btn-primary',
			'.image' => 'imagebutton',
			'.button' => 'btn btn-default',
		    
			'.container-buttons-class' => 'col-lg-offset-2',
			'.textarea-class' => 'form-control'
		),

		'label' => array(
			'container' => NULL,
			'suffix' => NULL,
			'requiredsuffix' => ' <span class="required-star">*</span>',
		    
			// used for label
			'.class' => 'sr-only',
		),

		'hidden' => array(
			'container' => 'div',
		),
	);
	
	/**
	 * Initializes form.
	 */
	protected function init()
	{
		parent::init();
		$label = & $this->wrappers['label'];
		$c = & $this->wrappers['control'];
		foreach ($this->form->getControls() as $name => $control) {
			if($control instanceof \Nette\Forms\Controls\TextArea || $control instanceof \Nette\Forms\Controls\SelectBox) {
				$control->setAttribute('class', $c['.textarea-class'] . ' ' . $control->getControlPrototype()->class);
			} elseif($control instanceof \Nette\Forms\Controls\SubmitButton === FALSE) {
				$control->setAttribute('placeholder', $control->getLabel()->getText());
			} elseif($control instanceof \Nette\Forms\Controls\SubmitButton) {
				if($name === 'reset') {
					$control->getControlPrototype()
						->class('btn btn-danger button red float-l');
				}
			}
			$control->getLabelPrototype()
				->class($label['.class'], TRUE);
		}
	}

}
