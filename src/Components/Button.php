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
        ICON_POSITION = 'icon_position',
	    CONFIRM = 'confirm',
	    TEXT = 'text',
	    TITLE = 'title',
        DISABLED = 'disabled',
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

	public function setType($type) {
		$this->option[self::TYPE] = $type;
		return $this;
	}

	public function setClassName($class_name) {
		$this->option[self::CLASS_NAME] = $class_name;
		return $this;
	}

    /**
     * @param $position string (left|right)
     * @return $this
     */
    public function setIconPosition($position) {
        $this->option[self::ICON_POSITION] = $position === 'right' ? 'right' : 'left';
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

    public function setDisabled($disabled = TRUE) {
        $this->option[self::DISABLED] = $disabled;
        return $this;
    }

	public function setAttributes(array $attributes) {
		$this->option[self::ATTRIBUTES] = $attributes;
		return $this;
	}

	/**
	 * Use setAttribute instead
	 *
	 * @deprecated
	 */
	public function addAttribute($key, $value, $append = FALSE) {
		return $this->setAttribute($key, $value, $append);
	}

	/**
	 * @param $key
	 * @param $value
	 * @param bool $append
	 * @return $this
	 */
	public function setAttribute($key, $value, $append = FALSE) {
		if($append && isset($this->option[self::ATTRIBUTES][$key])) {
			$this->option[self::ATTRIBUTES][$key] = $this->option[self::ATTRIBUTES][$key] . ' ' . $value;
		} else {
			$this->option[self::ATTRIBUTES][$key] = $value;
		}
		return $this;
	}

	protected function setDefaults() {
		return array(
		    self::TYPE => 'btn-primary',
		    self::ICON_POSITION => 'left',
		    self::DISABLED => FALSE,
		    self::TEXT => ''
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
			throw new Grid_Exception('Presenter is not set for Button.');
		}

		if (isset($this->option[self::BUTTON_CLASSES])) {
			$class = $this->option[self::BUTTON_CLASSES];
		} else {
			$class = 'btn btn-sm ' . (array_key_exists(self::CLASS_NAME, $this->option) ? ($this->option[self::CLASS_NAME] . ' ') : '');
			$class .= $this->option[self::TYPE];
		}

		$button = Html::el('a');
		if (array_key_exists(self::CONFIRM, $this->option)) {
			$button->addAttributes(array(
			    'onclick' => "return confirm('" . addslashes($this->getTranslator() ? $this->getTranslator()->translate($this->option[self::CONFIRM]) : $this->option[self::CONFIRM]) . "');"
			));
		}

		if (array_key_exists(self::TITLE, $this->option)) {
			$button->addAttributes(array(
			    'title' => $this->getTranslator() ? $this->getTranslator()->translate($this->option[self::TITLE]) : $this->option[self::TITLE]
			));
		}

		if (array_key_exists(self::ATTRIBUTES, $this->option) && is_array($this->option[self::ATTRIBUTES])) {
			foreach ($this->option[self::ATTRIBUTES] as $name => $value) {
				if ($value instanceof Link) {
                    if(!$this->option[self::DISABLED]) {
                        $output = $this->addLinkAttr($button, $name, $value);
                        if ($output === FALSE) {
                            $this->option[self::DISABLED] = TRUE;
                        }
                    }
				} else {
					$button->addAttributes(array(
					    $name => Link::parseValue($value, $this->data)
					));
				}
			}

		}

        if($this->option[self::DISABLED]) {
            $class .= ' disabled';
        }

        $button->addAttributes(array('class' => $class));

        $translated_text = $this->getTranslator() ? $this->getTranslator()->translate($this->option[self::TEXT]) : $this->option[self::TEXT];
        if($this->option[self::ICON_POSITION] === 'right') {
            $button->addHtml($translated_text);
        }

		if ((array_key_exists(self::ICON, $this->option) && is_string($this->option[self::ICON])) || array_key_exists(self::ICON_CLASSES, $this->option)) {
			if (isset($this->option[self::ICON_CLASSES])) {
				$attributes = array('class' => $this->option[self::ICON_CLASSES]);
			} else {
				$attributes = array('class' => 'glyphicon ' . $this->option[self::ICON]);
				if (isset($this->option[self::ICON_COLOR])) {
					$attributes['style'] = 'color:' . $this->option[self::ICON_COLOR];
				}
			}
            $button->addHtml(Html::el('b', $attributes));
            $button->addHtml(' ');
		}

        if($this->option[self::ICON_POSITION] === 'left') {
            $button->addHtml($translated_text);
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
			$used_component = $link->getUsedComponent();
			if (!is_null($used_component)) {
				if (is_string($used_component)) {
					$href_attribute = $this->presenter[$used_component]->link($to_href, $params);
				} elseif (is_array($used_component)) {
					$component = $this->presenter;
					foreach ($used_component as $component_name) {
						$component = $component[$component_name];
					}
					$href_attribute = $component->link($to_href, $params);
				} else {
					throw new Grid_Exception('Link::COMPONENT must be string or array, ' . gettype($used_component) . ' given.');
				}
				$button->addAttributes(array(
				    $attr_name => $href_attribute
				));
			} else {
				$button->addAttributes(array(
				    $attr_name => $this->presenter->link($to_href, $params)
				));
			}

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