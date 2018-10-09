<?php

namespace App\Adianti\Model\Form\Fields;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\Spinner;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeInt;

/**
 * Fields FieldInteger
 * Link between attribute table and form field Spinner
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
        $this->field = new Spinner($name, $min, $max, $step);

        parent::__construct($label ?? $name);

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
