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

	const DEFAULT_COUNT = 20;

	protected $parent;

	protected $callback;

	protected $checkCallback;

	protected $type;

	protected $name;

	protected $disabled = FALSE;

	protected $description;

	protected $page_limit;

	protected $keys = array();

	protected $aliases = array();

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

	public function setCheckCallback($callback) {
		Callback::check($callback);
		$this->checkCallback = $callback;
		return $this;
	}

	public function check($rowData) {
		if($this->checkCallback) {
			Callback::invokeArgs($this->checkCallback, array($rowData, $this));
		}
	}

	public function isDisabled() {
		return $this->disabled;
	}

	public function setDisabled($disabled = TRUE) {
		$this->disabled = $disabled;
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

	public function addAlias($for_key, $alias) {
		$this->aliases[$for_key] = $alias;
	}

	public function getTranslatedKey($key) {
		return isset($this->aliases[$key]) ? $this->aliases[$key] : $key;
	}

	public function invoke(array $args = array(), $name, $key) {
		return $this->callback ? Callback::invokeArgs($this->callback, $args) : NULL;
	}

	public function hasKey($key) {
		return isset($this->parent->parent[$this->name . $key]);
	}

	public function getKeys() {
		return $this->keys;
	}

	abstract public function render();

	abstract public function reset();

}