<?php

namespace Dvi\Adianti\Component\Model\Form\Fields;

use Dvi\Adianti\Model\DBFormField;
use Dvi\Adianti\Widget\Form\DRadioGroup;

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
class FieldRadio extends DBFormField
{

    public function __construct(string $name, string $type, bool $required = false, string $label = null)
    {
        $array = explode('_', $name);
        $field_name = array_pop($array);

        $label = $label ?? $field_name;

        parent::__construct($name, $type, $required, $label);

        $this->field = new DRadioGroup($name, $label, $required);
    }

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
