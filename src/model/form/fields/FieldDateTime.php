<?php

namespace Dvi\Adianti\Component\Model\Form\Fields;

use Adianti\Base\Lib\Validator\TRequiredValidator;
use Adianti\Base\Lib\Widget\Form\TDateTime;
use Dvi\Adianti\Model\DBFormField;

/**
 * Model DBDateTime
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Adianti
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class FieldDateTime extends DBFormField
{
    public function __construct(string $name, bool $required = false, string $label = null)
    {
        $array = explode('_', $name);
        $field_name = array_pop($array);

        $label = $label ?? $field_name;

        parent::__construct($name, 'datetime', $required, $label);

        $this->field = new TDateTime($name);
        $this->field->placeholder = $label;
        if ($required) {
            $this->field->addValidation($label, new TRequiredValidator());
        }
        $this->field->setDatabaseMask('yyyy-mm-dd hh:ii:ss');
    }

    public static function create(string $name, bool $required = false, string $label = null): FieldDateTime
    {
        $field = new FieldDateTime($name, $required, $label);
        return $field;
    }

    public function getField()
    {
        return $this->field;
    }

    public function mask(string $mask)
    {
        $this->field->setMask($mask);
        return $this;
    }
}
