<?php

namespace DataGrid;

use \Nette\Utils\Html;

/**
 * Description of \DataGrid\Button
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class Button {
	/**
	 * Possible option key
	 */

	const ID = 'id',
		STYLE = 'style',
		CLASS_NAME = 'class_name',
		ICON = 'icon',
		ICON_COLOR = 'icon_color',
		CONFIRM = 'confirm',
		TITLE = 'title',
		HREF = 'href',
		HREF_LINK = 'href_link',
		HREF_LINK_DATA = 'href_link_data',
		DATA_TITLE = 'data_title',
		DATA_HREF = 'data_href',
		DATA_HREF_LINK = 'data_href_link',
		DATA_HREF_LINK_DATA = 'data_href_link_data',
		MODAL = 'modal',
		ONLY_FROM_ID = 'only_from_id';

	/**
	 * Option for current button
	 *
	 * @var Array
	 */
	private $option = array();
	
	/**
	 * Row data for button
	 *
	 * @var Array
	 */
	private $data = array();

	/**
	 * Create instance
	 * 
	 * @param Array $option
	 * @param Array $data
	 * @throws \Exception
	 */
	public function __construct(array $option, $data = NULL) {
		if (key_exists(self::ID, $option) === FALSE) {
			throw new \Exception('ID can not be empty in button options.');
		}
		if (empty($data) === FALSE) {
			$this->data = $data;
		}
		$this->option = $option;
	}

	/**
	 * Create button
	 * 
	 * @param Array $data
	 * @return String
	 * @throws \Exception
	 */
	public function create($data = NULL) {
		if (empty($data) === FALSE) {
			$this->data = $data;
		}
		if (empty($this->data)) {
			throw new \Exception('Empty data');
		}
		if(isset($this->option[self::ONLY_FROM_ID]) && $this->data[$this->option[self::ID]] < $this->option[self::ONLY_FROM_ID]) {
			return '';
		}
		
		$presenter = \Nette\Environment::getApplication()->getPresenter();
		$icon = key_exists(self::ICON, $this->option) === FALSE ? 'glyphicon-pencil' : $this->option[self::ICON];
		$icon_color = key_exists(self::ICON_COLOR, $this->option) === FALSE ? '' : $this->option[self::ICON_COLOR];
		
		$button = Html::el('a', array('class' => ( key_exists(self::CLASS_NAME, $this->option) ? $this->option[self::CLASS_NAME] . ' ' : '' ) . 'btn btn-sm ' . ( key_exists(self::STYLE, $this->option) === FALSE ? '' : $this->option[self::STYLE] )));

		if (key_exists(self::MODAL, $this->option)) {
			$button->addAttributes(array(
			    'data-toggle' => 'modal',
			    'href' => '#defaultModal',
			    'data-title' => $this->option[self::MODAL]
			));
		}

		if (key_exists(self::CONFIRM, $this->option)) {
			$button->addAttributes(array(
			    'data-confirm' => $this->option[self::CONFIRM]
			));
		}

		if (key_exists(self::TITLE, $this->option)) {
			$button->addAttributes(array(
			    'title' => $this->option[self::TITLE]
			));
		}

		if (key_exists(self::HREF, $this->option)) {
			$button->addAttributes(array(
			    'href' => $this->option[self::HREF]
			));
		} else if (key_exists(self::HREF_LINK, $this->option)) {
			if (isset($this->option[self::HREF_LINK_DATA])) {
				foreach ($this->option[self::HREF_LINK_DATA] as $key => $value) {
					$params[$key] = \DataGrid\Column::parseValue($value, $this->data);
				}
			} else {
				$params = array();
			}
			$to_href = \DataGrid\Column::checkLinkPermission($this->option[self::HREF_LINK]);
			if ($to_href === FALSE) {
				return '';
			}
			$button->addAttributes(array(
			    'href' => $presenter->link($to_href, $params)
			));
		}
		if (key_exists(self::DATA_HREF, $this->option)) {
			$button->addAttributes(array(
			    'data-href' => $this->option[self::DATA_HREF]
			));
		} else if (key_exists(self::DATA_HREF_LINK, $this->option)) {
			if (isset($this->option[self::DATA_HREF_LINK_DATA])) {
				foreach ($this->option[self::DATA_HREF_LINK_DATA] as $key => $value) {
					$params[$key] = \DataGrid\Column::parseValue($value, $this->data);
				}
			} else {
				$params = array();
			}
			$to_href = \DataGrid\Column::checkLinkPermission($this->option[self::DATA_HREF_LINK]);
			if ($to_href === FALSE) {
				return '';
			}
			$button->addAttributes(array(
			    'data-href' => $presenter->link($to_href, $params)
			));
		}

		$button->add(Html::el('b', array('class' => 'glyphicon ' . $icon . ' ' . $icon_color)));
		return $button;
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