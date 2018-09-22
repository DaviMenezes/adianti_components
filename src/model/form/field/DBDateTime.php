<?php

namespace Dvi\Adianti\Model\Form\Field;

use Adianti\Base\Lib\Validator\TRequiredValidator;
use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\DDateTime;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeString;

/**
 * Model DateTime
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Adianti
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DBDateTime extends DBFormField
{
    public function __construct(string $name, string $label = null, bool $required = false)
    {
        $array = explode('-', $name);
        $field_name = array_pop($array);

        $label = $label ?? $field_name;

        parent::__construct($required, $label);

        $this->field = new DDateTime($name);
        $this->field->placeholder = $label;
        if ($required) {
            $this->field->addValidation($label, new TRequiredValidator());
        }

        $this->field->setLabel($label);

        $this->field->setMask('dd/mm/yyyy hh:ii:ss');
        $this->field->setDatabaseMask('yyyy-mm-dd hh:ii:ss');

        $this->setType(new FieldTypeString());
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

    public function setType($type)
    {
        $this->field->setType($type);
    }
}
