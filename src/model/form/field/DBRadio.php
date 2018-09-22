<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\DRadioGroup;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeString;

/**
 * FieldRadio
 *
 * @version    Dvi 1.0
 * @package    Fields
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DBRadio extends DBFormField
{
    public function __construct(string $name, bool $required = false, string $label = null)
    {
        $array = explode('-', $name);
        $field_name = array_pop($array);

        $label = $label ?? $field_name;

        parent::__construct($required, $label);

        $this->field = new DRadioGroup($name, $label, $required);

        $this->setType(new FieldTypeString());
    }

    public function setType($type)
    {
        $this->field->setType($type);
    }

    /**@return DRadioGroup*/
    public function getField()
    {
        return $this->field;
    }

    public function items(array $items)
    {
        $this->field->items($items);
        return $this;
    }
}
