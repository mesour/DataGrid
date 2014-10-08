<?php

namespace DataGrid\Render;

use \DataGrid\Column;

/**
 * Description of \DataGrid\Render\Renderer
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
abstract class Renderer{

	/**
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * @var Header
	 */
	protected $header;

	/**
	 * @var Body
	 */
	protected $body;

	public function setAttributes(array $attributes = array()) {
		$this->attributes = $attributes;
	}

	public function setBody(Body $body) {
		$this->body = $body;
	}

	public function setHeader(Header $header) {
		$this->header = $header;
	}

	abstract public function create();

}