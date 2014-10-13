<?php

namespace DataGrid\Extensions;

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
		if(!$this->private_session) {
			$this->private_session = $this->presenter->getSession()->getSection($this->parent->getGridName() . $this->getName());
		}
		return $this->private_session;
	}

	public function loadState(array $params) {
		$session = $this->getSession();
		if(!empty($params) && isset($params['settings'])) {
			foreach($params as $key => $val) {
				$session[$key] = $val;
			}
		} elseif(!empty($session)) {
			foreach($session as $key => $val) {
				$params[$key] = $val;
			}
		}
		parent::loadState($params);
	}

}