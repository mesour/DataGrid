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
class Dropdown extends Setting {

	/**
	 * Possible option key
	 */
	const NAME = 'name',
	    LINKS = 'links',
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
	 * @param \Nette\Application\UI\Presenter $presenter
	 * @param Array|NULL $data
	 * @throws \Mesour\DataGrid\Grid_Exception
	 */
	public function __construct(Presenter $presenter, array $option = array(), $data = NULL) {
		parent::__construct($option);
		if (empty($data) === FALSE) {
			$this->data = $data;
		}
		$this->presenter = $presenter;
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

	public function setSizeClass($size_class) {
		$this->option[self::SIZE_CLASS] = $size_class;
		return $this;
	}

	public function addHeader($name) {
		$this->option[self::LINKS][] = array('dropdown-header', $name);
		return $this;
	}

	public function addLink($href, $name, array $parameters = array(), $is_nette_link = TRUE, $component = NULL) {
		$this->option[self::LINKS][] = new Link(array(
		    Link::HREF => $href,
		    Link::PARAMS => $parameters,
		    Link::NAME => $name,
		    Link::COMPONENT => $component,
		    Link::USE_NETTE_LINK => $is_nette_link
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

		$container = Html::el('div', array('class' => 'dropdown'));

		$has_links = FALSE;
		$ul = Html::el('ul', array('class' => 'dropdown-menu', 'aria-labelledby' => 'dropdownMenu', 'role' => 'menu'));
		foreach ($this->option[self::LINKS] as $link) {
			if ($link instanceof Link) {
				$href = $link->create($this->data);
				if (!$href) {
					continue;
				}
				if (isset($separator)) {
					$ul->add($separator);
					unset($separator);
				}
				if (isset($header)) {
					$ul->add($header);
					unset($header);
				}
				list($to_href, $params, $name) = $href;

				if ($link->hasUseNetteLink()) {
					$used_component = $link->getUsedComponent();
					if (!is_null($used_component)) {
						if (is_string($used_component)) {
							$href_param = $this->presenter[$used_component]->link($to_href, $params);
						} elseif (is_array($used_component)) {
							$component = $this->presenter;
							foreach ($used_component as $component_name) {
								$component = $component[$component_name];
							}
							$href_param = $component->link($to_href, $params);
						} else {
							throw new Grid_Exception('Link::COMPONENT must be string or array, ' . gettype($used_component) . ' given.');
						}
					} else {
						$href_param = $this->presenter->link($to_href, $params);
					}
				} else {
					$href_param = $to_href;
				}

				$li = Html::el('li', array('role' => 'presentation'));
				$a = Html::el('a', array('role' => 'menuitem', 'tabindex' => '-1', 'href' => $href_param));
				$a->setText($name);

				$li->add($a);
				$ul->add($li);
				$has_links = TRUE;
			} elseif (is_string($link)) {
				$separator = Html::el('li', array('role' => 'presentation', 'class' => $link));
			} elseif (is_array($link) && isset($link[0]) && isset($link[1])) {
				$header = Html::el('li', array('role' => 'presentation', 'class' => $link[0]));
				$header->setText($this->getTranslator() ? $this->getTranslator()->translate($link[1]) : $link[1]);
			}
		}
		if ($has_links) {
			$button = Html::el('button', array('class' => 'btn ' . $this->option[self::TYPE] . ' dropdown-toggle ' . $this->option[self::SIZE_CLASS] . (' ' . $this->option[self::BUTTON_CLASS_NAME]), 'id' => 'dropdownMenu', 'type' => 'button', 'data-toggle' => 'dropdown'));
			$button->setHtml(($this->getTranslator() ? $this->getTranslator()->translate($this->option[self::NAME]) : $this->option[self::NAME]) . ' <span class="caret"></span>');
			$container->add($button);
			$container->add($ul);
		}
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