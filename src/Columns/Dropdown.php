<?php

namespace Mesour\DataGrid\Column;

use Mesour\DataGrid\Grid_Exception,
    Mesour\DataGrid\Components;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Dropdown extends Base {

	/**
	 * Possible option key
	 */
	const HEADER = 'header',
	    NAME = 'name',
	    LINKS = 'links',
	    TYPE = 'type',
	    BUTTON_CLASS_NAME = 'class_name',
	    SIZE_CLASS = 'size_class';

	public function setHeader($header) {
		$this->option[self::HEADER] = $header;
		return $this;
	}

	public function setName($name) {
		$this->option[self::NAME] = $name;
		return $this;
	}

	public function setButtonClassName($class_name) {
		$this->option[self::BUTTON_CLASS_NAME] = $class_name;
		return $this;
	}

	public function setLinks(array $links) {
		$this->option[self::LINKS] = $links;
		return $this;
	}

	public function addHeader($name) {
		$this->option[self::LINKS][] = array('dropdown-header', $name);
		return $this;
	}

	public function addLink($href, $name, array $parameters = array(), $is_nette_link = TRUE, $component = NULL) {
		$this->option[self::LINKS][] = new Components\Link(array(
		    Components\Link::HREF => $href,
		    Components\Link::PARAMS => $parameters,
		    Components\Link::NAME => $name,
		    Components\Link::COMPONENT => $component,
		    Components\Link::USE_NETTE_LINK => $is_nette_link
		));
		return $this;
	}

	public function addSeparator() {
		$this->option[self::LINKS][] = 'divider';
		return $this;
	}

	public function setType($type) {
		$this->option[self::TYPE] = $type;
		return $this;
	}

	protected function setDefaults() {
		return array(
		    self::TYPE => 'btn-default',
		    self::LINKS => array(),
		    self::NAME => 'Actions',
		    self::BUTTON_CLASS_NAME => '',
		    self::SIZE_CLASS => 'btn-xs'
		);
	}

	public function getHeaderAttributes() {
		$this->fixOption();
		if (array_key_exists(self::HEADER, $this->option) === FALSE) {
			throw new Grid_Exception('Option \DataGrid\DropdownColumn::HEADER is required.');
		}
		return array('class' => 'dropdown-column');
	}

	public function getHeaderContent() {
		return $this->getTranslator() ? $this->getTranslator()->translate($this->option[self::HEADER]) : $this->option[self::HEADER];
	}

	public function getBodyAttributes($data) {
		return parent::mergeAttributes($data, array('class' => 'right-buttons'));
	}

	public function getBodyContent($data) {
		$dropdown = new Components\Dropdown($this->grid->presenter, $this->option, $data);
		if ($this->getTranslator()) {
			$dropdown->setTranslator($this->getTranslator());
		}
		return $dropdown->create();
	}

}