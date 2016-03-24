<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Column;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class Template extends Filtering implements IExportable
{

	/** @var Mesour\DataGrid\TemplateFile */
	private $template;

	private $templateFile;

	private $block;

	public function setBlock($block)
	{
		$this->block = $block;
		return $this;
	}

	public function setTempDirectory($path)
	{
		if (!is_dir($path) && !is_writable($path)) {
			throw new Mesour\InvalidStateException('Temp directory must be directory and must be writable.');
		}
		if ($this->template) {
			unset($this->template);
		}
		$this->template = new Mesour\DataGrid\TemplateFile($path);
		return $this;
	}

	public function setTemplateFile($file)
	{
		if (file_exists($file)) {
			$this->templateFile = $file;
		} else {
			throw new Mesour\FileNotFoundException('Template file "' . $file . '" does not exist.');
		}
		return $this;
	}

	public function getHeaderAttributes()
	{
		return array_merge(
			[
				'class' => 'grid-column-' . $this->getName(),
			],
			parent::getHeaderAttributes()
		);
	}

	public function getBodyAttributes($data, $need = true, $rawData = [])
	{
		$attributes = parent::getBodyAttributes($data);
		$attributes['class'] = 'type-template';
		return parent::mergeAttributes($data, $attributes);
	}

	public function getBodyContent($data, $rawData)
	{
		$template = $this->getTemplate();

		$this->tryInvokeCallback([$this, $rawData, $template]);

		return trim($template);
	}

	private function getTemplate()
	{
		if (!$this->template) {
			throw new Mesour\InvalidStateException(
				'Temp directory is required, use setTempDirectory() on this column.'
			);
		}
		if (!$this->templateFile) {
			throw new Mesour\InvalidStateException('Template file is required, use setTemplateFile() on this column.');
		}
		$this->template->setFile(__DIR__ . '/Template/Template.latte');
		$this->template->_template_path = $this->templateFile;
		$this->template->_block = false;
		if (is_string($this->block)) {
			$this->template->_block = $this->block;
		}
		return $this->template;
	}

	public function attachToFilter(Mesour\DataGrid\Extensions\Filter\IFilter $filter, $hasCheckers)
	{
		parent::attachToFilter($filter, $hasCheckers);
		$item = $filter->addTextFilter($this->getName(), $this->getHeader());
		$item->setCheckers($hasCheckers);
	}

}
