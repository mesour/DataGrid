<?php

namespace Mesour\DataGrid\Components;

use Mesour\DataGrid\Column,
    Mesour\DataGrid\Grid_Exception,
    Nette\Utils\Html,
    Nette\Application\UI\Presenter;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class DropDownLink extends DropDownItem {

	const LINK = 'link',
		DISABLED = 'disabled';

	/**
	 *
	 * @var \Nette\Application\UI\Presenter
	 */
	protected $presenter;

	public $onRender = array();

	public function setLink(Link $link) {
		$this->option[self::LINK] = $link;
		return $this;
	}

	public function setPresenter(Presenter $presenter) {
		$this->presenter = $presenter;
		return $this;
	}

	public function setDisabled($disabled = TRUE) {
		$this->option[self::DISABLED] = $disabled;
		return $this;
	}

	public function setConfirm($text) {
		$this->setAttribute('onclick', "return confirm('" . $text . "');");
		return $this;
	}

	protected function setDefaults() {
		return array(
			self::DISABLED => FALSE
		);
	}

	public function create($data) {
		if($this->option[self::DISABLED] === TRUE) {
			return FALSE;
		}

		if (is_null($this->presenter)) {
			throw new Grid_Exception('Presenter is not set for DropDownLink.');
		}

		$link = $this->option[self::LINK]->create($data);
		if(!$link) {
			return FALSE;
		}
		list($to_href, $params, $name) = $link;

		if ($this->option[self::LINK]->hasUseNetteLink()) {
			$used_component = $this->option[self::LINK]->getUsedComponent();
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
		$a = Html::el('a', array('role' => 'menuitem', 'tabindex' => '-1', 'href' => $href_param));
		$a->setText($this->getTranslator() ? $this->getTranslator()->translate($name) : $name);

		if(!isset($this->option[self::ATTRIBUTES]['role'])) {
			$this->option[self::ATTRIBUTES]['role'] = 'presentation';
		}

		$li = Html::el('li', $this->option[self::ATTRIBUTES]);
		$li->addHtml($a);

		return $li;
	}

}