<?php

namespace DataGrid;

use \Nette\Utils\Html,
    \DataGrid\Grid_Exception;

/**
 * Description of \DataGrid\Link
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class Link {

	/**
	 * Possible option key
	 */
	const HREF = 'href',
	    	PARAMS = 'parameters',
	    	NAME = 'name';

	/**
	 * Option for this column
	 *
	 * @var Array
	 */
	protected $option = array();

	/**
	 * @param array $option
	 */
	public function __construct(array $option = array()) {
		if(!empty($option)) {
			$this->option = $option;
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

	public function setParameters(array $parameters) {
		$this->option[self::PARAMS] = $parameters;
		return $this;
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
			throw new Grid_Exception('Option \DataGrid\DropdownColumn::HREF is required.');
		}
		if (array_key_exists(self::PARAMS, $this->option) === FALSE) {
			$this->option[self::PARAMS] = array();
		}
		$link = Column\Base::getLink($this->option[self::HREF], $this->option[self::PARAMS], $data);
		if(!$link) {
			return FALSE;
		}
		$link[] = isset($this->option[self::NAME]) ? $this->option[self::NAME] : NULL;
		return $link;
	}

}