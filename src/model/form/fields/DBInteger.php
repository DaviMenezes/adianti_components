<?php

namespace App\Adianti\Component\Model\Form\Fields;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\DSpinner;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeInt;

/**
 * Fields FieldInteger
 *
 * @version    Dvi 1.0
 * @package    Fields
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DBInteger extends DBFormField
{
    public function __construct(string $name, int $min, int $max, int $step, bool $required = false, string $label = null)
    {
        $array = explode('-', $name);
        $field_name = array_pop($array);

        $label = $label ?? $field_name;

        parent::__construct($required, $label);

        $this->field = new DSpinner($name);
        $this->field->setRange($min, $max, $step);
        $this->field->setLabel($label);
        $this->field->placeholder = $this->getLabel();

        $this->setType(new FieldTypeInt());
    }

    public function getField()
    {
        return $this->field;
    }

    public function getLabel()
    {
        return ucfirst(parent::getLabel()?? $this->getField()->getName());
    }

    public function setType($type)
    {
        $this->field->setType($type);
    }
}
