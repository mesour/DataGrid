<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Column;

use Mesour\DataGrid\Extensions\Selection\ISelection;


/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class Selection extends BaseColumn implements IPrependedColumn
{

	/**
	 * @var ISelection
	 */
	protected $selection;

	public function getHeaderAttributes()
	{
		$this->selection = $this->getGrid()->getExtension('ISelection');
		return ['class' => 'act act-select'];
	}

	public function getHeaderContent()
	{
		return $this->selection->create()->create();
	}

	public function getBodyAttributes($data, $need = true, $rawData = [])
	{
		return parent::mergeAttributes($data, ['class' => 'grid-checkbox']);
	}

	public function getBodyContent($data, $rawData)
	{
		return $this->selection->createItem($data[$this->getGrid()->getSource()->getPrimaryKey()]);
	}
}