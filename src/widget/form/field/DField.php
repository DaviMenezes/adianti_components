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
    private $type;
    protected $error_msg;

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

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function sanitize()
    {
        $value = filter_var($this->getValue(), FILTER_SANITIZE_SPECIAL_CHARS);

        if ($this->type === 'url') {
            $value = filter_var($value, FILTER_SANITIZE_URL);
        } elseif ($this->type === 'string') {
            $value = filter_var($value, FILTER_SANITIZE_STRING);
        } elseif ($this->type === 'int') {
            $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        } elseif ($this->type === 'float') {
            $value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
        } elseif ($this->type === 'email') {
            $value = filter_var($value, FILTER_SANITIZE_EMAIL);
        } elseif ($this->type === 'ip') {
            $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        }
        $this->setValue($value);
    }

    public function filterValidate()
    {
        $value = $this->getValue();

        if ($this->type === 'url') {
            return $this->validating($value, FILTER_VALIDATE_URL);
        } elseif ($this->type === 'int') {
            return $this->validating($value, FILTER_VALIDATE_INT);
        } elseif ($this->type === 'float') {
            return $this->validating($value, FILTER_VALIDATE_FLOAT);
        } elseif ($this->type === 'email') {
            return $this->validating($value, FILTER_VALIDATE_EMAIL);
        } elseif ($this->type === 'ip') {
            return $this->validating($value, FILTER_VALIDATE_IP);
        } elseif ($this->type === 'bool') {
            return $this->validating($value, FILTER_VALIDATE_BOOLEAN);
        } elseif ($this->type === 'domain') {
            return $this->validating($value, FILTER_VALIDATE_DOMAIN);
        } elseif ($this->type === 'mac') {
            return $this->validating($value, FILTER_VALIDATE_MAC);
        } elseif ($this->type === 'regex') {
            return $this->validating($value, FILTER_VALIDATE_REGEXP);
        }
    }

    private function validating($value, $filter)
    {
        $result = filter_var($value, $filter);
        if (!$result) {
            $this->error_msg = 'O valor para '.$this->getLabel().' é inválido';
        }
        return $result;
    }
}
