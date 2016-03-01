<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Column;

use Mesour;


/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class Date extends InlineEdit implements IExportable
{

	private $format = 'Y-m-d';

	public function setFormat($format)
	{
		$this->format = $format;
		return $this;
	}

	public function getHeaderAttributes()
	{
		return [
			'class' => 'grid-column-' . $this->getName(),
		];
	}

	public function getBodyAttributes($data, $need = true, $rawData = [])
	{
		$attributes = parent::getBodyAttributes($data, $need, $rawData);
		if ($this->hasEditable()) {
			$attributes = array_merge($attributes, [
				'data-editable' => $this->getName(),
				'data-editable-type' => 'date',
				'data-date-format' => Mesour\Components\Utils\Helpers::convertDateToJsFormat($this->format),
				'data-editable-pickTime' => Mesour\Components\Utils\Helpers::isDateHasTime($this->format) ? 1 : 0,
			]);
		}
		$attributes['class'] = 'type-text';
		return parent::mergeAttributes(parent::getBodyAttributes($data), $attributes);
	}

	public function getBodyContent($data, $rawData)
	{
		if (!$data[$this->getName()]) {
			return '-';
		}
		if (is_numeric($data[$this->getName()])) {
			$date = new \DateTime();
			$date->setTimestamp($data[$this->getName()]);
		} elseif ($data[$this->getName()] instanceof \DateTime) {
			$date = $data[$this->getName()];
		} else {
			$date = new \DateTime($data[$this->getName()]);
		}
		$formattedDate = $date->format($this->format);

		$fromCallback = $this->tryInvokeCallback([$this, $rawData, $date, $formattedDate]);
		if ($fromCallback !== self::NO_CALLBACK) {
			return $fromCallback;
		}

		return $formattedDate;
	}

	public function attachToFilter(Mesour\DataGrid\Extensions\Filter\IFilter $filter, $hasCheckers)
	{
		parent::attachToFilter($filter, $hasCheckers);
		$item = $filter->addDateFilter($this->getName(), $this->getHeader());
		$item->setCheckers($hasCheckers);
	}

}
