<?php

namespace Mesour\DataGrid\Components;

use \Nette\Utils\Html,
    \Nette\Application\UI\Presenter,
    Mesour\DataGrid\Column,
    Mesour\DataGrid\Setting,
    Mesour\DataGrid\Grid_Exception;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class DropDown extends Setting {

	/**
	 * Possible option key
	 */
	const NAME = 'name',
	    LINKS = 'links',
	    DISABLED = 'disabled',
	    TYPE = 'type',
	    BUTTON_CLASS_NAME = 'class_name',
	    SIZE_CLASS = 'size_class';

	/**
	 * Row data for button
	 *
	 * @var Array
	 */
	private $data = array();

	/**
	 *
	 * @var \Nette\Application\UI\Presenter
	 */
	protected $presenter;

	/**
	 * @param array $option
	 * @param Presenter|NULL $presenter
	 * @param array $data
	 * @throws Grid_Exception
	 */
	public function __construct($option = array(), Presenter $presenter = NULL, $data = array()) {
		parent::__construct($option);
		if (empty($data) === FALSE) {
			$this->data = $data;
		}
		$this->presenter = $presenter;
	}

	public function setPresenter(Presenter $presenter) {
		$this->presenter = $presenter;
		return $this;
	}

	public function setName($name) {
		$this->option[self::NAME] = $name;
		return $this;
	}

	public function addLink($name, Link $link) {
		$link->setName($name);
		$drop_down_link = new DropDownLink;
		$drop_down_link->setLink($link);
		$this->option[self::LINKS][] = $drop_down_link;
		return $drop_down_link;
	}

	public function addHeader($name) {
		$header = new DropDownHeader;
		$header->setName($name);
		$this->option[self::LINKS][] = $header;
		return $header;
	}

	public function addSeparator() {
		$separator = new DropDownSeparator;
		$this->option[self::LINKS][] = $separator;
		return $separator;
	}

    public function setDisabled($disabled = TRUE) {
        $this->option[self::DISABLED] = $disabled;
        return $this;
    }

	public function setButtonClassName($class_name) {
		$this->option[self::BUTTON_CLASS_NAME] = $class_name;
		return $this;
	}

	public function setSizeClass($size_class) {
		$this->option[self::SIZE_CLASS] = $size_class;
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
		    self::DISABLED => FALSE,
		    self::SIZE_CLASS => 'btn-xs'
		);
	}

	/**
	 * Create button
	 *
	 * @param Array $data
	 * @return Html
	 * @throws \Mesour\DataGrid\Grid_Exception
	 */
	public function create($data = NULL) {
		if (empty($data) === FALSE) {
			$this->data = $data;
		}

		if (is_null($this->presenter)) {
			throw new Grid_Exception('Presenter is not set for DropDown.');
		}

		$container = Html::el('div', array('class' => 'dropdown'));

		$has_links = FALSE;
		$ul = Html::el('ul', array('class' => 'dropdown-menu', 'aria-labelledby' => 'dropdownMenu', 'role' => 'menu'));

        if(!$this->option[self::DISABLED]) {
            foreach ($this->option[self::LINKS] as $link) {
                if($link instanceof DropDownLink) {
                    $link->setPresenter($this->presenter);
                    if($this->getTranslator()) {
                        $link->setTranslator($this->getTranslator());
                    }
                    $link->onRender($this->data, $link);

                    $href = $link->create($this->data);
                    if($href === FALSE) {
                        continue;
                    }
                    if (isset($separator)) {
                        $ul->addHtml($separator);
                        unset($separator);
                    }
                    if (isset($header)) {
                        $ul->addHtml($header);
                        unset($header);
                    }

                    $ul->addHtml($href);
                    $has_links = TRUE;
                } elseif($link instanceof DropDownSeparator) {
                    $separator = $link->create($this->data);
                } elseif($link instanceof DropDownHeader) {
                    if($this->getTranslator()) {
                        $link->setTranslator($this->getTranslator());
                    }
                    $header = $link->create($this->data);
                }
            }
        }
        if(!$has_links) {
            $this->option[self::DISABLED] = TRUE;
        }

        $class = 'btn ' . $this->option[self::TYPE] . ' dropdown-toggle ' . $this->option[self::SIZE_CLASS] . (' ' . $this->option[self::BUTTON_CLASS_NAME]);
        if($this->option[self::DISABLED]) {
            $class .= ' disabled';
        }
        $button = Html::el('button', array('class' => $class, 'id' => 'dropdownMenu', 'type' => 'button', 'data-toggle' => 'dropdown'));
        $button->setHtml(($this->getTranslator() ? $this->getTranslator()->translate($this->option[self::NAME]) : $this->option[self::NAME]) . ' <span class="caret"></span>');
        $container->addHtml($button);
        $container->addHtml($ul);

		return $container;
	}

	/**
	 * See method create
	 *
	 * @return String
	 */
	public function __toString() {
		return $this->create()->render();
	}

}