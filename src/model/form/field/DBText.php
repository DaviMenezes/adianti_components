<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\DText;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeString;

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
class DBText extends DBFormField
{
    public function __construct(string $name, int $maxlength, int $height, bool $required = false, string $label = null)
    {
        $array = explode('-', $name);
        $field_name = array_pop($array);

        $label = $label ?? $field_name;

        $this->field = new DText($name, $label, $maxlength, $height);

        parent::__construct($required, $label);

        $this->setType(new FieldTypeString());
    }

    /**@return DText*/
    public function getField()
    {
        return $this->field;
    }

    public function setType($type)
    {
        $this->field->setType($type);
    }
}
