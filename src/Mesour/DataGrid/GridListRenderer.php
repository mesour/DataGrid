<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2016 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid;

use Mesour;

/**
 * @author Matouš Němec (http://mesour.com)
 */
class GridListRenderer extends Mesour\Table\ListRenderer
{

	protected $liAttributes = [];

	public function __construct(Mesour\Table\Render\IColumn $column)
	{
		parent::__construct($column);

		$this->getWrapperPrototype()
			->class('mesour-datagrid-list', true);
	}

}
