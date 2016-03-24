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
class EmptyData extends BaseColumn
{

	protected $text;

	public function setText($text)
	{
		$this->text = $this->getTranslator()->translate($text);
		return $this;
	}

	public function getHeaderAttributes()
	{
		return [];
	}

	public function getHeaderContent()
	{
		return null;
	}

	public function getBodyAttributes($data, $need = true, $rawData = [])
	{
		return ['colspan' => $data];
	}

	public function getBodyContent($data, $rawData)
	{
		$text = Mesour\Components\Utils\Html::el('p', ['class' => 'empty-data']);
		$text->setText($this->text);
		return $text;
	}

}
