<?php

namespace DataGrid\Components;

use \Nette\Utils\Html,
    \Nette\Application\UI\Presenter,
	DataGrid\Column,
	DataGrid\Setting,
	DataGrid\Grid_Exception;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Button extends Setting {

	/**
	 * Possible option key
	 */
	const TYPE = 'type',
	    CLASS_NAME = 'class_name',
	    BUTTON_CLASSES = 'button_classes',
	    ICON = 'icon',
	    ICON_COLOR = 'icon_color',
	    ICON_CLASSES = 'icon_classes',
	    CONFIRM = 'confirm',
	    TEXT = 'text',
	    TITLE = 'title',
	    ATTRIBUTES = 'attributes';

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
	 * @throws \DataGrid\Grid_Exception
	 */
	public function __construct(Presenter $presenter = NULL, array $option = array(), $data = NULL) {
		parent::__construct($option);
		if (empty($data) === FALSE) {
			$this->data = $data;
		}
		$this->presenter = $presenter;
	}

	public function setPresenter(Presenter $presenter) {
		$this->presenter = $presenter;
	}

	public function setType($type) {
		$this->option[self::TYPE] = $type;
		return $this;
	}

	public function setClassName($class_name) {
		$this->option[self::CLASS_NAME] = $class_name;
		return $this;
	}

	public function setButtonClasses($button_classes) {
		$this->option[self::BUTTON_CLASSES] = $button_classes;
		return $this;
	}

	public function setIcon($icon_class) {
		$this->option[self::ICON] = $icon_class;
		return $this;
	}

	public function setIconColor($css_color) {
		$this->option[self::ICON_COLOR] = $css_color;
		return $this;
	}

	public function setIconClasses($icon_classes) {
		$this->option[self::ICON_CLASSES] = $icon_classes;
		return $this;
	}

	public function setConfirm($confirm_text) {
		$this->option[self::CONFIRM] = $confirm_text;
		return $this;
	}

	public function setText($text) {
		$this->option[self::TEXT] = $text;
		return $this;
	}

	public function setTitle($title) {
		$this->option[self::TITLE] = $title;
		return $this;
	}

	public function setAttributes(array $attributes) {
		$this->option[self::ATTRIBUTES] = $attributes;
		return $this;
	}

	public function addAttribute($name, $value) {
		$this->option[self::ATTRIBUTES][$name] = $value;
		return $this;
	}

	protected function setDefaults() {
		return array(
		    self::TYPE => 'btn-primary',
		    self::TEXT => ''
		);
	}

	/**
	 * Create button
	 *
	 * @param Array $data
	 * @return Html
	 * @throws \DataGrid\Grid_Exception
	 */
	public function create($data = NULL) {
		if (empty($data) === FALSE) {
			$this->data = $data;
		}

		if(is_null($this->presenter)) {
			throw new Grid_Exception('Presenter is not set for Button.');
		}

		if(isset($this->option[self::BUTTON_CLASSES])) {
			$class = $this->option[self::BUTTON_CLASSES];
		} else {
			$class = 'btn btn-sm ' . (array_key_exists(self::CLASS_NAME, $this->option) ? ($this->option[self::CLASS_NAME] . ' ') : '');
			$class .= $this->option[self::TYPE];
		}


		$button = Html::el('a', array('class' => $class));
		$button->setText($this->option[self::TEXT]);

		if (array_key_exists(self::CONFIRM, $this->option)) {
			$button->addAttributes(array(
			    'onclick' => "return confirm('" . addslashes($this->option[self::CONFIRM]) . "');"
			));
		}

		if (array_key_exists(self::TITLE, $this->option)) {
			$button->addAttributes(array(
			    'title' => $this->option[self::TITLE]
			));
		}

		if (array_key_exists(self::ATTRIBUTES, $this->option) && is_array($this->option[self::ATTRIBUTES])) {
			foreach($this->option[self::ATTRIBUTES] as $name => $value) {
				if($value instanceof Link) {
					$output = $this->addLinkAttr($button, $name, $value);
					if($output === FALSE) {
						return Html::el('span');
					}
				} else {
					$button->addAttributes(array(
					    $name => Link::parseValue($value, $this->data)
					));
				}
			}

		}

		if((array_key_exists(self::ICON, $this->option) && is_string($this->option[self::ICON])) || array_key_exists(self::ICON_CLASSES, $this->option)) {
			if(isset($this->option[self::ICON_CLASSES])) {
				$attributes = array('class' => $this->option[self::ICON_CLASSES]);
			} else {
				$attributes = array('class' => 'glyphicon ' . $this->option[self::ICON]);
				if(isset($this->option[self::ICON_COLOR])) {
					$attributes['style'] = 'color:' . $this->option[self::ICON_COLOR];
				}
			}
			$button->add(Html::el('b', $attributes));
		}
		return $button;
	}

	public function addLinkAttr(& $button, $attr_name, Link $link) {
		$href = $link->create($this->data);
		if (!$link->hasUseNetteLink()) {
			$button->addAttributes(array(
			    $attr_name => reset($href)
			));
		} else {
			if ($href === FALSE) {
				return FALSE;
			}
			list($to_href, $params) = $href;
			$button->addAttributes(array(
			    $attr_name => $this->presenter->link($to_href, $params)
			));
		}
		return TRUE;
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