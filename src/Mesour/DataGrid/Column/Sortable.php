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
class Sortable extends BaseColumn implements IPrependedColumn
{

	use Mesour\Icon\HasIcon;

	protected $arrowsIcon = 'arrows';

	/**
	 * @return Mesour\UI\Button
	 */
	public function getButton()
	{
		if (!isset($this['button'])) {
			$this['button'] = new Mesour\UI\Button();
			$this['button']->setSize('btn-sm')
				->setType('default')
				->setAttribute('class', 'move handler', true)
				->setAttribute('href', '#')
				->setIcon($this->arrowsIcon);
		}
		return $this['button'];
	}

	public function getHeaderAttributes()
	{
		return ['class' => 'sortable-column'];
	}

	public function getHeaderContent()
	{
		$icon = $this->createNewIcon($this->arrowsIcon . ' grid-move');
		return $icon;
	}

	public function getBodyAttributes($data, $need = true, $rawData = [])
	{
		return parent::mergeAttributes($data, ['class' => 'grid-sortable']);
	}

	public function getBodyContent($data, $rawData)
	{
		$this->getButton()->setOption('data', $data);
		return $this->getButton()->create();
	}

}
