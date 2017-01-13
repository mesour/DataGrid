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
class Number extends InlineEdit implements IExportable
{

	use Mesour\Components\Security\Authorised;
	use Mesour\Icon\HasIcon;

	private $decimals = 0;

	private $unit = null;

	private $decimalPoint = '.';

	private $thousandSeparator = ',';

	public function setDecimals($decimals)
	{
		$this->decimals = (int) $decimals;
		return $this;
	}

	public function setDecimalPoint($decimalPoint = '.')
	{
		$this->decimalPoint = $decimalPoint;
		return $this;
	}

	public function setUnit($unit)
	{
		$this->unit = $unit;
		return $this;
	}

	public function setThousandsSeparator($thousandSeparator = ',')
	{
		$this->thousandSeparator = $thousandSeparator;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getDecimals()
	{
		return $this->decimals;
	}

	/**
	 * @return string
	 */
	public function getDecimalPoint()
	{
		return $this->decimalPoint;
	}

	/**
	 * @return null
	 */
	public function getUnit()
	{
		return $this->unit;
	}

	/**
	 * @return string
	 */
	public function getThousandSeparator()
	{
		return $this->thousandSeparator;
	}

	public function getHeaderAttributes()
	{
		return parent::mergeAttributes(parent::getHeaderAttributes(), [
			'class' => 'grid-column-' . $this->getName(),
		]);
	}

	public function getBodyAttributes($data, $need = true, $rawData = [])
	{
		$attributes = parent::getBodyAttributes($data);
		$attributes['class'] = 'type-text';
		return parent::mergeAttributes(parent::getBodyAttributes($data), $attributes);
	}

	public function getBodyContent($data, $rawData)
	{
		$formatted = number_format($data[$this->getName()], $this->decimals, $this->decimalPoint, $this->thousandSeparator)
			. ($this->unit ? (' ' . $this->unit) : '');

		$fromCallback = $this->tryInvokeCallback([$this, $rawData, $formatted]);
		if ($fromCallback !== self::NO_CALLBACK) {
			return $fromCallback;
		}
		return $formatted;
	}

	public function attachToFilter(Mesour\DataGrid\Extensions\Filter\IFilter $filter, $hasCheckers)
	{
		parent::attachToFilter($filter, $hasCheckers);
		$item = $filter->addNumberFilter($this->getName(), $this->getHeader());
		$this->setUpFilterItem($item, $hasCheckers);
	}

}
