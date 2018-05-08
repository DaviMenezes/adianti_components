<?php

namespace Dvi\Adianti\Component\Model\Form\Fields;

use Dvi\Adianti\Model\DBFormField;
use Dvi\Adianti\Widget\Form\DText;

/**
 * Model FieldText
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Adianti
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class FieldText extends DBFormField
{
    public function __construct(string $name, int $maxlength, int $height, bool $required = false, string $label = null)
    {
        $array = explode('_', $name);
        $field_name = array_pop($array);

        $label = $label ?? $field_name;

        parent::__construct($name, 'text', $required, $label);

        $this->field = new DText($name, $label, $maxlength, $height, true, $required);
    }

    public static function create(
        string $name,
        int $maxlength,
        int $height,
        bool $required = false,
        $label = null
    ):FieldText {
        return new FieldText($name, $maxlength, $height, $required, $label);
    }

    public function getField()
    {
        return $this->field;
    }

    public function setType($type)
    {
        $this->field->setType($type);
    }
}
