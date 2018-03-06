<?php

namespace Dvi\Adianti\Component\Model\Form\Fields;

use Adianti\Base\Lib\Validator\TRequiredValidator;
use Adianti\Base\Lib\Widget\Form\TNumeric;
use Dvi\Adianti\Model\DBFormField;

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
class FieldCurrency extends DBFormField
{
    public function __construct(
        string $name,
        int $decimals,
        string $decimalsSeparator,
        string $thousandSeparator,
        string $type,
        bool $required = false,
        string $label = null
    ) {
        $array = explode('_', $name);
        $field_name = array_pop($array);

        $label = $label ?? $field_name;

        parent::__construct($name, $type, $required, $label);


        $this->field = new TNumeric($name, $decimals, $decimalsSeparator, $thousandSeparator);
        $this->field->placeholder = $label;
        if ($required) {
            $this->field->addValidation(ucfirst($label), new TRequiredValidator());
        }
        $this->field->setTip($label);
        $this->field->setLabel($label);
    }

    public function getField()
    {
        return $this->field;
    }
}
