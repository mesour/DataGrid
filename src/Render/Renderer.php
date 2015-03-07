<?php

namespace Mesour\DataGrid\Render;

use Mesour\DataGrid\Column;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
abstract class Renderer extends Attributes {

	/**
	 * @var Header
	 */
	protected $header;

	/**
	 * @var Body
	 */
	protected $body;

	public function setBody(Body $body) {
		$this->body = $body;
	}

	public function setHeader(Header $header) {
		$this->header = $header;
	}

	abstract public function create();

}