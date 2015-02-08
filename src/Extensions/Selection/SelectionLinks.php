<?php

namespace Mesour\DataGrid\Extensions;

use Mesour\DataGrid\Column,
	Nette\Object;
use Nette\ComponentModel\IComponent;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class SelectionLinks extends Object {

	private $parent;

	public function __construct(IComponent $parent) {
		$this->parent = $parent;
	}

	private $links = array();

	/**
	 * @param $name
	 * @return SelectionLink
	 */
	public function addLink($name) {
		$link = new SelectionLink();
		if ($this->parent->getTranslator()) {
			$link->setTranslator($this->parent->getTranslator());
		}
		$link->setName($name);
		$this->links[$link->getFixedName()] = $link;
		return $link;
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