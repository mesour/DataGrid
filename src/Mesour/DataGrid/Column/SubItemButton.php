<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015-2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Column;

use Mesour\UI\Button;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class SubItemButton extends BaseColumn
{

	private $columnName;

	private $key = -1;

	private $opened = false;

	private $twoRows = false;

	public function setColumnName($name)
	{
		$this->columnName = $name;
		return $this;
	}

	public function setKey($key)
	{
		$this->key = $key;
		return $this;
	}

	public function setOpened($opened = true)
	{
		$this->opened = (bool) $opened;
		return $this;
	}

	public function setTwoRows($twoRows = true)
	{
		$this->twoRows = (bool) $twoRows;
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
		$attributes = ['colspan' => $data, 'class' => 'subgrid-button'];
		if ($this->twoRows) {
			$attributes['rowspan'] = 2;
		}
		return parent::mergeAttributes([], $attributes);
	}

	public function getBodyContent($data, $rawData)
	{
		$button = $this['currentButton'] = new Button;

		$button->setType('info')
			->setSize('btn-sm btn-sm-grid')
			->setAttribute('data-mesour', 'ajax')
			->setAttribute('href', $this->getGrid()->getExtension('ISubItem')->createLink('toggleItem', ['key' => $this->key, 'name' => $this->columnName]));
		if ($this->opened) {
			$button->setIcon('minus');
		} else {
			$button->setIcon('plus');
		}

		$this->tryInvokeCallback([$rawData, $this, $button]);

		return $button->create();
	}

}
