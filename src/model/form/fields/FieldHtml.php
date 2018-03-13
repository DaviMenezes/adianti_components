<?php

namespace Dvi\Adianti\Component\Model\Form\Fields;

use Adianti\Base\Lib\Validator\TMaxLengthValidator;
use Adianti\Base\Lib\Validator\TRequiredValidator;
use Adianti\Base\Lib\Widget\Form\THtmlEditor;
use Dvi\Adianti\Model\DBFormField;

/**
 * Fields FieldHtml
 *
 * @version    Dvi 1.0
 * @package    Fields
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class FieldHtml extends DBFormField
{
    public function __construct(string $name, int $maxlength, int $height, bool $required = false, string $label = null)
    {
        $array = explode('_', $name);
        $field_name = array_pop($array);

        $label = $label ?? $field_name;

        parent::__construct($name, 'text', $required, $label);

        $this->field = new THtmlEditor($name);
        $this->field->setLabel($label);
        if ($required) {
            $this->field->addValidation(ucfirst($label), new TRequiredValidator());
        }

        if ($maxlength > 0) {
            $this->field->addValidation(ucfirst($label), new TMaxLengthValidator(), [$maxlength]);
        }

        $this->field->setSize('100%', $height);
    }

    public function getField()
    {
        return $this->field;
    }
}
