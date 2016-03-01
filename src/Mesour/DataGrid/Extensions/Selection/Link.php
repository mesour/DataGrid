<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\Selection;

use Mesour;


/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 *
 * @method null onCall($items)
 */
class Link extends Mesour\Object
{
	private $name;

	private $fixed_name;

	private $confirm = false;

	private $permission = false;

	private $ajax = true;

	/** @var Mesour\Components\Localization\ITranslator */
	private $translator;

	public $onCall = [];

	/** @var Mesour\UI\Button */
	private $button;

	public function __construct($name, Mesour\UI\Button $button, Mesour\Components\Localization\ITranslator $translator)
	{
		$this->translator = $translator;

		$name = $this->translator->translate($name);
		$this->fixed_name = Mesour\Components\Utils\Helpers::webalize($name);
		$this->button = $button;
		$this->button->setAttribute('data-mesour-selection', 'ajax');
		$this->button->setAttribute('href', '#');
		$this->button->setAttribute('data-name', $this->getFixedName());
		$this->name = $name;
		return $this;
	}

	public function setAjax($ajax)
	{
		$this->ajax = (bool)$ajax;
		$this->button->setAttribute('data-mesour-selection', $this->ajax ? 'ajax' : 'none');
		return $this;
	}

	public function setGridSelection($dataMesourGridSelection)
	{
		$this->button->setAttribute('data-mesour-gridselection', $dataMesourGridSelection);
		return $this;
	}

	public function setConfirm($confirm)
	{
		$this->confirm = $this->translator->translate($confirm);
		$this->button->setAttribute('data-confirm', $this->confirm);
		return $this;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setPermission($resource, $privilege)
	{
		$this->button->setPermission($resource, $privilege);
		return $this;
	}

	public function isAllowed()
	{
		return $this->button->isAllowed();
	}

	/**
	 * @return Mesour\UI\Button
	 */
	public function getButton()
	{
		return $this->button;
	}

	public function getFixedName()
	{
		return $this->fixed_name;
	}

	public function __clone()
	{

	}
}