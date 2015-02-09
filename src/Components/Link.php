<?php

namespace Mesour\DataGrid\Components;

use Mesour\DataGrid\Setting,
	Mesour\DataGrid\Grid_Exception;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Link extends Setting {

	/**
	 * Possible option key
	 */
	const HREF = 'href',
	    PARAMS = 'parameters',
	    NAME = 'name',
	    USE_NETTE_LINK = 'use_nette',
	    COMPONENT = 'component';

	/**
	 * Valid permission callback
	 *
	 * @var Mixed
	 */
	static public $checkPermissionCallback;

	public function __construct($destination = array(), $args = array()) {
		if(is_array($destination)) {
			parent::__construct($destination);
		} else if(is_string($destination)) {
			parent::__construct();
			$this->option[self::HREF] = $destination;
			$this->option[self::PARAMS] = $args;
		}
	}

	public function setName($name) {
		$this->option[self::NAME] = $name;
		return $this;
	}

	public function setHref($href) {
		$this->option[self::HREF] = $href;
		return $this;
	}

	public function setComponent($component) {
		$this->option[self::COMPONENT] = $component;
		return $this;
	}

	public function setParameters(array $parameters) {
		$this->option[self::PARAMS] = $parameters;
		return $this;
	}

	public function setUseNetteLink($nette = TRUE) {
		$this->option[self::USE_NETTE_LINK] = $nette;
		return $this;
	}

	public function addParameter($name, $value) {
		$this->option[self::PARAMS][$name] = $value;
		return $this;
	}

	/**
	 * Check permissions for link
	 *
	 * @param String $link
	 * @return String|FALSE
	 */
	static public function checkLinkPermission($link) {
		if (is_callable(self::$checkPermissionCallback)) {
			return call_user_func_array(self::$checkPermissionCallback, array($link));
		}
		return $link;
	}

	/**
	 * Returns true or array($href, $params) for $presenter->link()
	 *
	 * @param $link
	 * @param array $arguments
	 * @param null $data
	 * @return array|bool
	 */
	static public function getLink($link, array $arguments = array(), $data = NULL) {
		$params = array();
		if (!empty($arguments)) {
			foreach ($arguments as $key => $value) {
				$params[$key] = self::parseValue($value, is_null($data) ? array() : $data);
			}
		}
		$to_href = self::checkLinkPermission($link);
		if ($to_href === FALSE) {
			return FALSE;
		}
		return array($to_href, $params);
	}

	/**
	 * Parse value with {identifier}
	 *
	 * @param String $value
	 * @param Array $data
	 * @return Array
	 */
	static public function parseValue($value, $data) {
		if ((is_array($data) || $data instanceof \ArrayAccess) && substr($value, 0, 1) === '{' && substr($value, -1) === '}') {
			$key = substr($value, 1, strlen($value) - 2);
			if (array_key_exists($key, $data)) {
				return $data[$key];
			} else {
				return $value;
			}
		} else {
			return $value;
		}
	}

	protected function setDefaults() {
		return array(
		    self::USE_NETTE_LINK => TRUE,
		    self::PARAMS => array(),
		    self::COMPONENT => NULL
		);
	}

	public function hasUseNetteLink() {
		return $this->option[self::USE_NETTE_LINK];
	}

	public function getUsedComponent() {
		return $this->option[self::COMPONENT];
	}

	/**
	 * Create link
	 *
	 * @param null $data
	 * @return false|array list($to_href, $params, $name)
	 * @throws Grid_Exception
	 */
	public function create($data = NULL) {
		if (array_key_exists(self::HREF, $this->option) === FALSE) {
			throw new Grid_Exception('Option ' . __CLASS__ . '::HREF is required.');
		}
		if ($this->hasUseNetteLink()) {
			$link = self::getLink($this->option[self::HREF], $this->option[self::PARAMS], $data);
			if (!$link) {
				return FALSE;
			}
		} else {
			$link = array($this->option[self::HREF], $this->option[self::PARAMS]);
		}
		$link[] = isset($this->option[self::NAME]) ? $this->option[self::NAME] : NULL;
		return $link;
	}

}