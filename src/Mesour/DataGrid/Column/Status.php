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
 * @method Mesour\DataGrid\Column\Status\IStatusItem getComponent($name, $need = FALSE)
 */
class Status extends Filtering implements IExportable
{

    static public $no_active_class = 'no-active-button';

    /**
     * @param $name
     * @return Mesour\DataGrid\Column\Status\StatusButton
     */
    public function addButton($name)
    {
        $button = new Mesour\DataGrid\Column\Status\StatusButton($name);
        $this->addComponent($button);
        return $button;
    }

    /**
     * @param $name
     * @return Mesour\DataGrid\Column\Status\StatusDropDown
     */
    public function addDropDown($name)
    {
        $dropDown = new Mesour\DataGrid\Column\Status\StatusDropDown($name);
        $this->addComponent($dropDown);
        return $dropDown;
    }

    public function addComponent(Mesour\Components\ComponentModel\IComponent $component, $name = NULL)
    {
        if (!$component instanceof Mesour\DataGrid\Column\Status\IStatusItem) {
            throw new Mesour\InvalidArgumentException('Can add children for status column only if is instance of Mesour\DataGrid\Column\Status\IStatusItem.');
        }
        return parent::addComponent($component, $name);
    }

    public function setPermission($resource = NULL, $privilege = NULL)
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
            'class' => 'grid-column-' . $this->getName() . ' column-status'
        ], parent::getHeaderAttributes());
    }

    public function getBodyAttributes($data, $need = TRUE, $rawData = [])
    {
        $class = 'button-component';
        $active_count = 0;
        foreach ($this as $button) {
            /** @var Mesour\DataGrid\Column\Status\IStatusItem $button */
            if ($button->isActive($this->getName(), $data)) {
                $class .= ' is-' . $button->getStatus();
                $active_count++;
            }
        }
        if ($active_count === 0) {
            $class .= ' ' . self::$no_active_class;
        }
        return parent::mergeAttributes($data, ['class' => $class]);
    }

    public function getBodyContent($data, $rawData, $export = FALSE)
    {
        $buttons = $export ? [] : '';

        $activeCount = 0;
        foreach ($this as $button) {
            /** @var Mesour\DataGrid\Column\Status\IStatusItem $button */
            $isActive = FALSE;

            if ($button->isActive($this->getName(), $data)) {
                $isActive = TRUE;
            }

            $this->tryInvokeCallback([$rawData, $this, $isActive]);

            if ($isActive && !$export) {
                $buttons .= $button->create($data) . ' ';
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

        $item = $filter->addTextFilter($this->getName(), $this->getHeader(), $statuses);
        $item->setMainFilter(FALSE);
        $item->setCheckers($hasCheckers);
    }

}
