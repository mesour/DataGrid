<?php

namespace DataGrid\Column;

use \Nette\Utils\Html,
    \DataGrid\Grid_Exception,
    \DataGrid\Link;

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
	    	LINKS = 'links',
		TYPE = 'type';

	public function setText($text) {
		$this->option[self::TEXT] = $text;
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

	public function setLink($href, $name, array $parameters = array()) {
		$this->option[self::LINKS][] = new Link(array(
		    Link::HREF => $href,
		    Link::PARAMS => $parameters,
		    Link::NAME => $name
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
		if (array_key_exists(self::LINKS, $this->option) === FALSE) {
			$this->option[self::LINKS] = array();
		}
		if (array_key_exists(self::TYPE, $this->option) === FALSE) {
			$this->option[self::TYPE] = 'btn-default';
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
		$container = Html::el('div', array('class' => 'dropdown'));

		$has_links = FALSE;
		$ul = Html::el('ul', array('class' => 'dropdown-menu', 'aria-labelledby' => 'dropdownMenu', 'role' => 'menu'));
		foreach ($this->option[self::LINKS] as $link) {
			if($link instanceof Link) {
				$link = $link->create($this->data);
				if(!$link) {
					continue;
				}
				list($to_href, $params, $name) = $link;

				$li = Html::el('li', array('role' => 'presentation'));
				$a = Html::el('a', array('role' => 'menuitem', 'tabindex' => '-1', 'href' => $this->grid->presenter->link($to_href, $params)));
				$a->setText($name);

				$li->add($a);
				$ul->add($li);
				$has_links = TRUE;
			} elseif(is_string($link)) {
				$li = Html::el('li', array('role' => 'presentation', 'class' => $link));
				$ul->add($li);
			} elseif(is_array($link) && isset($link[0]) && isset($link[1])) {
				$li = Html::el('li', array('role' => 'presentation', 'class' => $link[0]));
				$li->setText($link[1]);
				$ul->add($li);
			}
		}
		if($has_links) {
			$button = Html::el('button', array('class' => 'btn ' . $this->option[self::TYPE] . ' dropdown-toggle btn-xs', 'id' => 'dropdownMenu', 'type' => 'button', 'data-toggle' => 'dropdown'));
			$button->setHtml('Dropdown <span class="caret"></span>');
			$container->add($button);
			$container->add($ul);
		}

		$span->add($container);
		return $span;
	}

}