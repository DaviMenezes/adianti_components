<?php

namespace App\Adianti\Component\Model\Form\Fields;

use Adianti\Base\Lib\Widget\Form\TSpinner;
use Dvi\Adianti\Model\DBFormField;
use Dvi\Adianti\Widget\Form\DSpinner;

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
class FieldInteger extends DBFormField
{
    public function __construct(string $name, int $min, int $max, int $step, bool $required = false, string $label = null, string $type = 'numeric')
    {
        $array = explode('_', $name);
        $field_name = array_pop($array);

        $label = $label ?? $field_name;

        parent::__construct($name, $type, $required, $label);

        $this->field = new DSpinner($name);
        $this->field->setRange($min, $max, $step);
        $this->field->setLabel($label);
        $this->field->placeholder = $this->getLabel();
    }

    public function getField()
    {
        return $this->field;
    }

    public function getLabel()
    {
        return ucfirst(parent::getLabel()?? $this->getName());
    }

    public function setType($type)
    {
        $this->field->setType($type);
    }
}