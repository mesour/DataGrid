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
class Number extends InlineEdit implements IExportable
{

    private $decimals = 0;

    private $unit = NULL;

    private $decimalPoint = '.';

    private $thousandSeparator = ',';

    public function setDecimals($decimals)
    {
        $this->decimals = (int)$decimals;
        return $this;
    }

    public function setDecimalPoint($decimal_point = '.')
    {
        $this->decimalPoint = $decimal_point;
        return $this;
    }

    public function setUnit($unit)
    {
        $this->unit = $unit;
        return $this;
    }

    public function setThousandsSeparator($thousand_separator = ',')
    {
        $this->thousandSeparator = $thousand_separator;
        return $this;
    }

    public function getHeaderAttributes()
    {
        return parent::mergeAttributes(parent::getHeaderAttributes(), [
            'class' => 'grid-column-' . $this->getName()
        ]);
    }

    public function getBodyAttributes($data, $need = TRUE, $rawData = [])
    {
        $attributes = parent::getBodyAttributes($data);
        if ($this->hasEditable()) {
            $attributes = array_merge($attributes, [
                'data-editable' => $this->getName(),
                'data-editable-type' => 'number',
                'data-separator' => $this->thousandSeparator,
                'data-unit' => is_null($this->unit) ? '' : $this->unit,
            ]);
        }
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
        $item->setCheckers($hasCheckers);
    }

}
