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
    public function __construct(string $name, string $label = null)
    {
        $this->field = new DRadioGroup($name, $label);

        parent::__construct($label);
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
