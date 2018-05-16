<?php

namespace Dvi\Adianti\Component\Model\Form\Fields;

use Dvi\Adianti\Model\DBFormField;
use Dvi\Adianti\Widget\Form\DDate;

/**
 * Model DBDate
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Components
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class FieldDate extends DBFormField
{
    public function __construct(string $name, bool $required = false, string $label = null)
    {
        $array = explode('_', $name);
        $field_name = array_pop($array);

        $label = $label ?? $field_name;

        parent::__construct($name, 'datetime', $required, $label);

        $this->field = new DDate($name, $label, $required);
    }

    public static function create(string $name, bool $required = false, string $label = null): FieldDate
    {
        $field = new FieldDate($name, $required, $label);
        return $field;
    }

    public function getField():DDate
    {
        return $this->field;
    }

    public function setType($type)
    {
        $this->field->setType($type);
    }
}
