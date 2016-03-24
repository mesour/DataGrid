<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid;

use Latte\Engine;
use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class TemplateFile extends \stdClass implements Mesour\Components\Utils\IString
{

	/** @var Engine */
	private static $engine;

	private $file;

	private $parameters = [];

	public function __construct($tempDir)
	{
		if (!class_exists('Latte\Engine')) {
			throw new Mesour\InvalidStateException('TemplateFile required composer package "latte/latte".');
		}
		if (!self::$engine) {
			self::$engine = new Engine;
		}
		self::$engine->setTempDirectory($tempDir);
	}

	public function setFile($file)
	{
		$this->file = $file;
	}

	public function render($toString = false)
	{
		if (!$toString) {
			self::$engine->render($this->file, $this->parameters);
		} else {
			return self::$engine->renderToString($this->file, $this->parameters);
		}
		return '';
	}

	public function __toString()
	{
		try {
			return $this->render(true);
		} catch (\Exception $e) {
			trigger_error($e->getMessage(), E_USER_WARNING);
			return '';
		}
	}

	public function __set($name, $value)
	{
		$this->parameters[$name] = $value;
	}

	public function __get($name)
	{
		if (!isset($this->parameters[$name])) {
			throw new Mesour\OutOfRangeException('Parameter with name ' . $name . ' does not exist.');
		}
		return $this->parameters[$name];
	}

	public function __isset($name)
	{
		return isset($this->parameters[$name]);
	}

	public function __unset($name)
	{
		unset($this->parameters[$name]);
	}

}
