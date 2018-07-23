<?php

namespace Dvi\Adianti\Component\Model\Form\Fields;

use Adianti\Base\Lib\Validator\TRequiredValidator;
use Adianti\Base\Lib\Widget\Form\TField;
use Dvi\Adianti\Componente\Model\Form\Fields\DNumeric;
use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeString;

/**
 * Fields FieldCurrency
 *
 * @version    Dvi 1.0
 * @package    Fields
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DBCurrency extends DBFormField
{
    public function __construct(
        string $name,
        int $decimals,
        string $decimalsSeparator,
        string $thousandSeparator,
        bool $required = false,
        string $label = null
    ) {
        $array = explode('-', $name);
        $field_name = array_pop($array);

        $label = $label ?? $field_name;

        parent::__construct($required, $label);


        $this->field = new DNumeric($name, $decimals, $decimalsSeparator, $thousandSeparator);
        $this->field->placeholder = $label;
        if ($required) {
            $this->field->addValidation(ucfirst($label), new TRequiredValidator());
        }
        $this->field->setTip($label);
        $this->field->setLabel($label);

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
