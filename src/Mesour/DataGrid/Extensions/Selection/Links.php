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
 */
class Links extends Mesour\Object implements Mesour\Components\Utils\IString
{

	/** @var Mesour\Components\Control\IControl */
	private $parent;

	/** @var Mesour\UI\DropDown */
	private $dropDown;

	/** @var Link[] */
	private $links = [];

	public function __construct(ISelection $parent)
	{
		$this->parent = $parent;
		$this->dropDown = new Mesour\UI\DropDown('links', $parent);
		$this->dropDown
			->setAttribute('class', 'dropdown selection-dropdown');

		$this->dropDown
			->getMainButton()
			->setText('Selected')
			->setDisabled(true);
	}

	public function setParent(ISelection $parent)
	{
		$this->parent = $parent;
		$this->dropDown = clone $this->dropDown;
		$parent->removeComponent('links');
		$parent->addComponent($this->dropDown, 'links');
	}

	/**
	 * @param string $name
	 * @return Link
	 */
	public function addLink($name)
	{
		$button = $this->dropDown->addButton($this->parent->getTranslator()->translate($name));
		$link = new Link($name, $button, $this->parent->getTranslator());
		$this->links[$link->getFixedName()] = $link;

		return $link;
	}

	/**
	 * @param array $attributes
	 * @return $this
	 */
	public function addDivider(array $attributes = [])
	{
		$this->dropDown->addDivider($attributes);
		return $this;
	}

	/**
	 * @param string $text
	 * @param array $attributes
	 * @return $this
	 */
	public function addHeader($text, array $attributes = [])
	{
		$this->dropDown->addHeader($text, $attributes);
		return $this;
	}

	/**
	 * @param string $fixedName
	 * @return Link
	 */
	public function getLink($fixedName)
	{
		return $this->links[$fixedName];
	}

	/**
	 * @return Mesour\UI\DropDown
	 */
	public function getDropDown()
	{
		return $this->dropDown;
	}

	public function create($data = [])
	{
		$this->dropDown->getControlPrototype()
			->addAttributes([
				'data-mesour-selectiondropdown' => $this->parent->createLinkName(),
			]);
		foreach ($this->getLinks() as $link) {
			$link->setGridSelection($this->parent->createLinkName());
		}
		$this->dropDown->setOption('data', $data);
		return $this->dropDown->create();
	}

	public function __toString()
	{
		return (string) $this->create();
	}

	/**
	 * @return Link[]
	 */
	public function getLinks()
	{
		$out = [];
		foreach ($this->links as $link) {
			if ($link->isAllowed()) {
				$out[] = $link;
			}
		}
		return $out;
	}

}
