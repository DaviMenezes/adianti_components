<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\RadioGroup;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeString;

/**
 * FieldRadio
 * Link between attribute table and form field RadioGroup
 * @version    Dvi 1.0
 * @package    Fields
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DBRadio extends DBFormField
{
    public function __construct(string $name, string $label = null)
    {
        $this->field = new RadioGroup($name, $label);

        parent::__construct($label ?? $name);
    }

    public function setType($type)
    {
        $this->field->setType($type);
    }

    /**@return RadioGroup*/
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
