<?php

namespace App\Adianti\Model\Form\Fields;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\DSpinner;
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
    public function __construct(string $name, int $min, int $max, int $step, string $label = null)
    {
        $array = explode('-', $name);
        $field_name = array_pop($array);

        $label = $label ?? $field_name;

        parent::__construct(false, $label);

        $this->field = new DSpinner($name, $min, $max, $step);
        $this->field->setLabel($this->getLabel());
        $this->field->setTip($this->getLabel());
        $this->field->placeholder = $label;

        $this->setType(new FieldTypeInt());
    }

    public function getLabel()
    {
        return ucfirst($this->label ?? $this->getField()->getName());
    }

    public function setType($type)
    {
        $this->field->setType($type);
    }
}
