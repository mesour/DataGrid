<?php

namespace Mesour\DataGrid\Extensions;

use Nette\ComponentModel\IComponent;
use Nette\Object;
use Nette\Utils\Callback;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
abstract class Item extends Object {

	protected $parent;

	protected $callback;

	protected $type;

	protected $name;

	protected $description;

	protected $page_limit;

	public function __construct(IComponent $parent, $name, $description = NULL) {
		$this->parent = $parent;
		$this->name = $name;
		$this->page_limit = $this->parent->getParent()->getPageLimit();
		$this->description = $description;
	}

	public function setCallback($callback) {
		Callback::check($callback);
		$this->callback = $callback;
		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function setDescription($description) {
		return $this->description = $description;
	}

	public function getDescription() {
		return $this->description ? $this->description : $this->name;
	}

	public function invoke(array $args = array()) {
		return Callback::invokeArgs($this->callback, $args);
	}

	abstract public function render();

	abstract public function reset();

}