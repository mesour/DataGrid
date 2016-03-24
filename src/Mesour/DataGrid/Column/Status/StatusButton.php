<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Column\Status;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class StatusButton extends Mesour\UI\Button implements IStatusItem
{

	private $status;

	private $selectionTitle = null;

	private $statusName;

	private $callback;

	private $callbackArgs = [];

	public function __construct($name = null, Mesour\Components\ComponentModel\IContainer $parent = null)
	{
		parent::__construct($name, $parent);

		$this->setSize('btn-sm');
	}

	public function setStatus($status, $statusName, $selectionTitle = null)
	{
		$this->status = $status;
		$this->statusName = $this->getTranslator()->translate($statusName);
		$this->setTooltip($this->statusName, 'right');
		$this->selectionTitle = !is_null($selectionTitle) ? $this->getTranslator()->translate($selectionTitle) : null;
		return $this;
	}

	/**
	 * @return array   [$this->status => $this->statusName]
	 */
	public function getStatusOptions()
	{
		return is_null($this->selectionTitle) ? null : [$this->status => $this->selectionTitle];
	}

	public function getStatusName()
	{
		return $this->statusName;
	}

	public function setCallback($callback)
	{
		Mesour\Components\Utils\Helpers::checkCallback($callback);
		$this->callback = $callback;
		return $this;
	}

	public function setCallbackArguments(array $arguments)
	{
		$this->callbackArgs = $arguments;
		return $this;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function isActive($columnName, $data)
	{
		if (!$this->callback) {
			return $data[$columnName] == $this->status ? true : false;
		} else {
			$args = [$data];
			if (count($this->callbackArgs) > 0) {
				$args = array_merge($args, $this->callbackArgs);
			}
			return Mesour\Components\Utils\Helpers::invokeArgs($this->callback, $args);
		}
	}

}
