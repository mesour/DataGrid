<?php

namespace Mesour\DataGrid\Extensions;

use Mesour\DataGrid\Column,
    \Nette\Object,
    Nette\Localization\ITranslator,
    Nette\Utils\Strings;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class SelectionLink extends Object {

	private $name;

	private $fixed_name;

	private $confirm = FALSE;

	private $ajax = TRUE;

	/**
	 * @var ITranslator
	 */
	private $translator;

	public $onCall = array();

	public function setName($name) {
		if($this->translator) {
			$name = $this->translator->translate($name);
		}
		$this->fixed_name = Strings::webalize($name);
		$this->name = $name;
		return $this;
	}

	public function setAjax($ajax) {
		$this->ajax = (bool) $ajax;
		return $this;
	}

	public function setConfirm($confirm) {
		if($this->translator) {
			$confirm = $this->translator->translate($confirm);
		}
		$this->confirm = $confirm;
		return $this;
	}

	public function setTranslator(ITranslator $translator) {
		$this->translator = $translator;
	}

	public function getName() {
		return $this->name;
	}

	public function getFixedName() {
		return $this->fixed_name;
	}

	public function getConfirm() {
		return $this->confirm;
	}

	public function isAjax() {
		return $this->ajax;
	}

}