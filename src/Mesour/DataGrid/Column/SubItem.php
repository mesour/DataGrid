<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Column;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class SubItem extends EmptyData
{

	public function setText($text)
	{
		$this->text = $text;
		return $this;
	}

	public function getBodyAttributes($data, $need = true, $rawData = [])
	{
		return parent::mergeAttributes([], ['colspan' => $data]);
	}

	public function getBodyContent($data, $rawData)
	{
		$this->tryInvokeCallback([$rawData, $this, $this->text]);
		return $this->text;
	}

}
