<?php

namespace Mesour\DataGrid\Extensions;

use Mesour\DataGrid\Column,
    \Nette\Application\UI\Control;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class SelectionLinks extends Control {

	private $links = array();

	/**
	 * @param $name
	 * @return SelectionLink
	 */
	public function addLink($name) {
		$fixed_name = \Nette\Utils\Strings::webalize($name);
		$this->links[$fixed_name] = new SelectionLink();
		if ($this->parent->getTranslator()) {
			$this->links[$fixed_name]->setTranslator($this->parent->getTranslator());
		}
		$this->links[$fixed_name]->setName($name);
		return $this->links[$fixed_name];
	}

	/**
	 * @param $fixed_name
	 * @return SelectionLink
	 */
	public function getLink($fixed_name) {
		return $this->links[$fixed_name];
	}

	/**
	 * @return array
	 */
	public function getLinks() {
		return $this->links;
	}

}