<?php

namespace Mesour\DataGrid\Extensions;

use \Nette\Application\UI\Control;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class BaseControl extends Control {

	/**
	 * @var \Nette\Http\SessionSection
	 */
	private $private_session;

	/**
	 * @persistent Array
	 */
	public $settings = array();

	public function getSession() {
		if (!$this->private_session) {
			$this->private_session = $this->presenter->getSession()->getSection($this->parent->getGridName() . $this->getName());
		}
		return $this->private_session;
	}

	public function loadState(array $params) {
		$session = $this->getSession();
		if (!empty($params) && isset($params['settings'])) {
			$settings = array();
			foreach ($params['settings'] as $key => $val) {
				$settings[$key] = $val;
			}
			$session['settings'] = $settings;
		} elseif (!empty($session['settings'])) {
			foreach ($session['settings'] as $key => $val) {
				$params['settings'][$key] = $val;
			}
		}
		if($this->getName() === 'ordering') {
			//echo $this->parent->getGridName() . $this->getName();
			//print_r($params);
		}
		parent::loadState($params);
	}

	protected function createTemplate($class = NULL) {
		$template = parent::createTemplate($class);
		$template->setTranslator($this->parent["translator"]);
		return $template;
	}
}