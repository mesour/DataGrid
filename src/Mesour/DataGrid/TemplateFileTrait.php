<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\Template;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
trait TemplateTrait
{

	private $file;

	private $block;

	/**
	 * @var Mesour\Template\ITemplate
	 */
	private $templateEngine;

	/**
	 * @var Mesour\UI\TemplateFile
	 */
	private $templateFile;

	public function setTempDir($tempDir)
	{
		$this->templateFile = new Mesour\UI\TemplateFile($this->getEngine(), $tempDir);
		return $this;
	}

	public function setBlock($block)
	{
		$this->block = $block;
		if ($this->templateFile) {
			$this->templateFile->setBlock($block);
		}
		return $this;
	}

	public function setFile($file)
	{
		$this->file = $file;
		if ($this->templateFile) {
			$this->templateFile->setFile($file);
		}
		return $this;
	}

	public function setTemplateEngine(Mesour\Template\ITemplate $template)
	{
		$this->templateEngine = $template;
	}

	public function getEngine()
	{
		if (!$this->templateEngine) {
			$this->templateEngine = new Mesour\Template\Latte\LatteTemplate();
		}
		return $this->templateEngine;
	}

	public function renderTemplate()
	{
		$template = $this->getTemplateFile();
		return $template->render(true);
	}

	protected function getTemplateFile()
	{
		if (!$this->templateFile) {
			throw new Mesour\InvalidStateException('Temp dir is required. User setTempDir.');
		}
		if (!$this->file) {
			throw new Mesour\InvalidStateException('Template file is required. User setFile.');
		} else {
			$this->templateFile->setFile($this->file);
		}
		if ($this->block) {
			$this->templateFile->setBlock($this->block);
		}
		return $this->templateFile;
	}

}
