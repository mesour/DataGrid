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
use Mesour\Table\Render;


/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class Container extends Filtering implements IExportable, IContainer
{

    /**
     * @param $name
     * @param string|null $header
     * @return Text
     */
    public function addText($name, $header = NULL)
    {
        $column = new Text;
        $this->setColumn($column, $name, $header);
        return $column;
    }

    /**
     * @param $name
     * @param string|null $header
     * @return Date
     */
    public function addDate($name, $header = NULL)
    {
        $column = new Date;
        $this->setColumn($column, $name, $header);
        return $column;
    }

    /**
     * @param $name
     * @param string|null $header
     * @return self
     */
    public function addContainer($name, $header = NULL)
    {
        $column = new self;
        $column->setFiltering(FALSE)
            ->setOrdering(FALSE);
        $this->setColumn($column, $name, $header);
        return $column;
    }

    /**
     * @param $name
     * @param string|null $header
     * @return Image
     */
    public function addImage($name, $header = NULL)
    {
        $column = new Image;
        $this->setColumn($column, $name, $header);
        return $column;
    }

    /**
     * @param $name
     * @param string|null $header
     * @return Status
     */
    public function addStatus($name, $header = NULL)
    {
        $column = new Status;
        $this->setColumn($column, $name, $header);
        return $column;
    }

    /**
     * @param $name
     * @param string|null $header
     * @return Template
     */
    public function addTemplate($name, $header = NULL)
    {
        $column = new Template;
        $this->setColumn($column, $name, $header);
        return $column;
    }

    /**
     * @param $name
     * @return Mesour\UI\Button(
     * @throws Mesour\InvalidArgumentException
     */
    public function addButton($name)
    {
        $button = new Mesour\UI\Button($name);
        $button->setSize('btn-sm');
        $this->addComponent($button);
        return $button;
    }

    /**
     * @param $name
     * @return Mesour\UI\DropDown
     * @throws Mesour\InvalidArgumentException
     */
    public function addDropDown($name)
    {
        $dropDown = new Mesour\UI\DropDown($name);
        $dropDown->getMainButton()
            ->setSize('btn-sm');
        $this->addComponent($dropDown);
        return $dropDown;
    }

    public function attachToFilter(Mesour\DataGrid\Extensions\Filter\IFilter $filter, $hasCheckers)
    {
        parent::attachToFilter($filter, $hasCheckers);
        $item = $filter->addTextFilter($this->getName(), $this->getHeader());
        $item->setCheckers($hasCheckers);
    }

    protected function setColumn(Render\IColumn $column, $name, $header = NULL)
    {
        $column->setHeader($header);
        return $this[$name] = $column;
    }

    public function getHeaderAttributes()
    {
        return [
            'class' => 'grid-column-' . $this->getName() . ' column-container'
        ];
    }

    public function getBodyAttributes($data, $need = TRUE, $rawData = [])
    {
        return parent::getBodyAttributes($data, FALSE, $rawData);
    }

    public function getBodyContent($data, $rawData, $export = FALSE)
    {
        if (
            !isset($data->{$this->getName()})
            && (property_exists($data, $this->getName()) && !is_null($data->{$this->getName()}))
            && ($this->hasFiltering() || $this->hasOrdering())
        ) {
            throw new Mesour\OutOfRangeException('Column with name ' . $this->getName() . ' does not exists in data source.');
        }

        $onlyButtons = TRUE;
        $container = Mesour\Components\Utils\Html::el('span', ['class' => 'container-content']);
        foreach ($this as $control) {
            if(!$control instanceof Mesour\UI\Button && !$control instanceof Mesour\UI\DropDown) {
                $onlyButtons = FALSE;
            }
            $span = Mesour\Components\Utils\Html::el('span');

            if ($control instanceof Render\IColumn) {
                $span->addAttributes($control->getHeaderAttributes());
                $span->addAttributes($control->getBodyAttributes($data));
            }

            $fromCallback = $this->tryInvokeCallback([$this, $rawData, $span, $control]);

            if ($fromCallback === self::NO_CALLBACK) {
                if ($control instanceof Render\IColumn) {
                    $content = $control->getBodyContent($data, $rawData);
                    if (!is_null($content)) {
                        $span->add($content);
                    }
                } elseif ($control instanceof Mesour\Components\Control\IOptionsControl) {
                    $control->setOption('data', $data);
                    $span->add($control->create());
                } else {
                    $span->add($control->render());
                }
            } else {
                $span->add($fromCallback);
            }

            $container->add($span);
            $container->add(' ');
        }
        if($onlyButtons) {
            $container->class('only-buttons', TRUE);
        }
        return $export ? trim(strip_tags($container)) : $container;
    }

}