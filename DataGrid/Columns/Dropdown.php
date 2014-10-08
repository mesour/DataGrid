<?php

namespace DataGrid\Column;

use \DataGrid\Grid_Exception,
    \DataGrid\Components;

/**
 * Description of \DataGrid\Column\Dropdown
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class Dropdown extends Base {

	/**
	 * Possible option key
	 */
	const TEXT = 'text',
	    NAME = 'name',
	    LINKS = 'links',
	    TYPE = 'type',
	    BUTTON_CLASS_NAME = 'class_name',
	    SIZE_CLASS = 'size_class';

	public function setText($text) {
		$this->option[self::TEXT] = $text;
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

	public function addLink($href, $name, array $parameters = array(), $is_nette_link = TRUE) {
		$this->option[self::LINKS][] = new Components\Link(array(
		    Components\Link::HREF => $href,
		    Components\Link::PARAMS => $parameters,
		    Components\Link::NAME => $name,
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
		if (array_key_exists(self::TEXT, $this->option) === FALSE) {
			throw new Grid_Exception('Option \DataGrid\DropdownColumn::TEXT is required.');
		}
		return array('class' => 'dropdown-column');
	}

	public function getHeaderContent() {
		return $this->option[self::TEXT];
	}

	public function getBodyAttributes($data) {
		return array('class' => 'right-buttons');
	}

	public function getBodyContent($data) {
		$dropdown = new Components\Dropdown($this->grid->presenter, $this->option, $data);
		return $dropdown->create();
	}

}