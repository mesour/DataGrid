<?php

namespace DataGrid\Column;

use \Nette\Utils\Html,
    \DataGrid\Grid_Exception,
    \DataGrid\Utils\Link,
    \DataGrid\Utils;

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

	public function setHeader($name) {
		$this->option[self::LINKS][] = array('dropdown-header', $name);
		return $this;
	}

	public function setLink($href, $name, array $parameters = array(), $is_nette_link = TRUE) {
		$this->option[self::LINKS][] = new Link(array(
		    Link::HREF => $href,
		    Link::PARAMS => $parameters,
		    Link::NAME => $name,
		    Link::USE_NETTE_LINK => $is_nette_link
		));
		return $this;
	}

	public function setSeparator() {
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

	/**
	 * Create HTML header
	 * 
	 * @return \Nette\Utils\Html
	 * @throws \DataGrid\Grid_Exception
	 */
	public function createHeader() {
		parent::createHeader();

		if (array_key_exists(self::TEXT, $this->option) === FALSE) {
			throw new Grid_Exception('Option \DataGrid\DropdownColumn::TEXT is required.');
		}
		$th = Html::el('th', array('class' => 'dropdown-column'));
		$th->setText($this->option[self::TEXT]);
		return $th;
	}

	/**
	 * Create HTML body
	 *
	 * @param mixed $data
	 * @param string $container
	 * @return Html|void
	 */
	public function createBody($data, $container = 'td') {
		parent::createBody($data);

		$span = Html::el($container, array('class' => 'right-buttons'));

		$dropdown = new Utils\Dropdown($this->grid->presenter, $this->option, $data);

		$span->add($dropdown->create());

		return $span;
	}

}