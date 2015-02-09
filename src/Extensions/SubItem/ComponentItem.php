<?php

namespace Mesour\DataGrid\Extensions;

use Nette\ComponentModel\IContainer;
use Nette\Utils\Callback;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class ComponentItem extends Item {

	public function __construct(IContainer $parent, $name, $description = NULL, $component = NULL) {
		parent::__construct($parent, $name, $description);
		$i = 0;
		while ($i <= (is_null($this->page_limit) ? self::DEFAULT_COUNT : $this->page_limit)) {
			if (!$component instanceof IContainer) {
				Callback::invokeArgs($component, array($this->parent->getParent(), $name . $i));
			} else {
				$this->parent->getParent()->addComponent($component, $name . $i);
			}
			$this->keys[] = $i;
			$i++;
		}
	}

	public function render($key = NULL) {
		if (is_null($key)) {
			return '';
		}
		$component = $this->parent->parent[$this->name . $this->getTranslatedKey($key)];
		return $component;
	}

	public function invoke(array $args = array(), $name, $key) {
		$arguments = array($this->parent->parent[$name . $key]);
		$arguments = array_merge($arguments, $args);
		return parent::invoke($arguments, $name, $key);
	}

	public function reset() {

	}

}