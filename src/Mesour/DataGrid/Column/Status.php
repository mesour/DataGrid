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
 *
 * @method Mesour\DataGrid\Column\Status\IStatusItem[] getComponents()
 * @method Mesour\DataGrid\Column\Status\IStatusItem getComponent($name, $need = false)
 */
class Status extends Filtering implements IExportable
{

	static public $no_active_class = 'no-active-button';

	/**
	 * @param string $name
	 * @return Mesour\DataGrid\Column\Status\StatusButton
	 */
	public function addButton($name)
	{
		$button = new Mesour\DataGrid\Column\Status\StatusButton($name);
		$this->addComponent($button);
		return $button;
	}

	/**
	 * @param string $name
	 * @return Mesour\DataGrid\Column\Status\StatusDropDown
	 */
	public function addDropDown($name)
	{
		$dropDown = new Mesour\DataGrid\Column\Status\StatusDropDown($name);
		$this->addComponent($dropDown);
		return $dropDown;
	}

	public function addComponent(Mesour\Components\ComponentModel\IComponent $component, $name = null)
	{
		if (!$component instanceof Mesour\DataGrid\Column\Status\IStatusItem) {
			throw new Mesour\InvalidArgumentException('Can add children for status column only if is instance of Mesour\DataGrid\Column\Status\IStatusItem.');
		}
		return parent::addComponent($component, $name);
	}

	public function setPermission($resource = null, $privilege = null)
	{
		foreach ($this->getComponents() as $component) {
			$component->setPermission($resource, $privilege);
			//todo: na dropdownu udělat také set permission aby to nastavilo práva na všech buttonech
		}
		return $this;
	}

	public function getHeaderAttributes()
	{
		return array_merge([
			'class' => 'grid-column-' . $this->getName() . ' column-status',
		], parent::getHeaderAttributes());
	}

	public function getBodyAttributes($data, $need = true, $rawData = [])
	{
		$class = 'button-component';
		$activeCount = 0;
		foreach ($this as $button) {
			/** @var Mesour\DataGrid\Column\Status\IStatusItem $button */
			if ($button->isActive($this->getName(), $data)) {
				$class .= ' is-' . $button->getStatus();
				$activeCount++;
			}
		}
		if ($activeCount === 0) {
			$class .= ' ' . self::$no_active_class;
		}
		return parent::mergeAttributes($data, ['class' => $class]);
	}

	public function getBodyContent($data, $rawData, $export = false)
	{
		$buttons = $export ? [] : '';

		$activeCount = 0;
		foreach ($this as $button) {
			/** @var Mesour\DataGrid\Column\Status\IStatusItem $button */
			$isActive = false;

			if ($button->isActive($this->getName(), $data)) {
				$isActive = true;
			}

			$this->tryInvokeCallback([$rawData, $this, $isActive]);

			$button->setOption('data', $data);
			if ($isActive && !$export) {
				$buttons .= $button->create() . ' ';
				$activeCount++;
			} elseif ($isActive && $export) {
				$buttons[] = $button->getStatusName();
			}
		}

		$container = Mesour\Components\Utils\Html::el('div', ['class' => 'status-buttons buttons-count-' . $activeCount]);
		$container->setHtml($export ? implode('|', $buttons) : $buttons);

		return $export ? trim(strip_tags($container)) : $container;
	}

	public function attachToFilter(Mesour\DataGrid\Extensions\Filter\IFilter $filter, $hasCheckers)
	{
		parent::attachToFilter($filter, $hasCheckers);

		$statuses = [];
		foreach ($this->getComponents() as $component) {
			$statuses[$component->getStatus()] = $component->getStatusName();
		}

		$filter->setCustomReference($this->getName(), $statuses);

		$item = $filter->addTextFilter($this->getName(), $this->getHeader(), $statuses);
		$item->setMainFilter(false);
		$item->setCheckers($hasCheckers);
		$item->setReferenceSettings(Mesour\DataGrid\Extensions\Filter\FilterExtension::PREDEFINED_KEY);
	}

}
