<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Componente\Model\Form\Fields\Numeric;
use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeString;

/**
 * Fields FieldCurrency
 * Link between attribute table and form field Numeric
 * @version    Dvi 1.0
 * @package    Fields
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DBNumeric extends DBFormField
{
    public function __construct(string $name, int $decimals, string $decimalsSeparator, string $thousandSeparator, string $label = null)
    {
        $this->field = new Numeric($name, $decimals, $decimalsSeparator, $thousandSeparator);

        parent::__construct($label ?? $name);

        $this->setType(new FieldTypeString());
    }

    public function getField()
    {
        return $this->field;
    }

    public function setType($type)
    {
        $this->field->setType($type);
    }
}
