<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Validator\TMaxLengthValidator;
use Adianti\Base\Lib\Validator\TRequiredValidator;

/**
 * Field DField
 *
 * @package    Field
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait DField
{
    private $ucfirstLabel;
    private $field_disabled;

    public function prepare(string $placeholder = null, bool $required = false, bool $tip = true, int $max_length = null)
    {
        $label = str_replace('_', ' ', $placeholder);

        $this->setLabel($label);

        if ($placeholder) {
            $this->placeholder = $label;
        }

        $this->ucfirstLabel = ucfirst($label);

        if (method_exists($this, 'setMaxLength') and $max_length) {
            $this->setMaxLength($max_length);
            $this->addValidation($this->ucfirstLabel, new TMaxLengthValidator(), [$max_length]);
        }

        if ($required) {
            $this->addValidation($this->ucfirstLabel, new TRequiredValidator());
        }

        if ($tip) {
            $this->setTip($this->ucfirstLabel);
        }
    }

    public function addValidations(array $array_validations)
    {
        foreach ($array_validations as $validation) {
            $this->addValidation($this->ucfirstLabel, $validation);
        }
    }

    public function setValueTest($string)
    {
        parent::setValue($string);
    }

    public function disable($disable = true)
    {
        $this->field_disabled = $disable;

        if ($disable) {
            $this->class = 'tfield_disabled';
            $this->readonly = '1';
        }
    }

    public function isDisabled()
    {
        return $this->field_disabled;
    }
}
