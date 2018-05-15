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
    protected $error_msg = array();
    protected $required;

    public function prepare(string $placeholder = null, bool $required = false, bool $tip = true, int $max_length = null)
    {
        $label = str_replace('_', ' ', $placeholder);

        $this->required = $required;

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
        $filter_type = [
            'url' => FILTER_VALIDATE_URL,
            'int' => FILTER_VALIDATE_INT,
            'float' => FILTER_VALIDATE_FLOAT,
            'email' => FILTER_VALIDATE_EMAIL,
            'ip' => FILTER_VALIDATE_IP,
            'bool' => FILTER_VALIDATE_BOOLEAN,
            'domain' => FILTER_VALIDATE_DOMAIN,
            'mac' => FILTER_VALIDATE_MAC,
            'regex' => FILTER_VALIDATE_REGEXP
        ];

        if (!in_array($this->type, array_keys($filter_type))) {
            return true;
        }

        $result = filter_var($this->getValue(), $filter_type[$this->type]);
        if (!$result) {
            $this->error_msg[] = 'O valor para ' . $this->getLabel() . ' é inválido';
        }

        return $result;
    }

    public function validating()
    {
        $this->validateRequired();

        if (method_exists($this, 'sanitize')) {
            $this->sanitize();
        }
        if ($this->required() and method_exists($this, 'filterValidate')) {
            $this->filterValidate();
        }

        return count($this->error_msg) ? false : true;
    }

    public function getErrorValidation()
    {
        $msg_errors = false;
        foreach ($this->error_msg as $item) {
            $msg_errors .= $item;
        }
        return $msg_errors;
    }

    public function required()
    {
        return $this->required;
    }

    protected function validateRequired(): bool
    {
        if ($this->required and empty($this->getValue())) {
            $this->error_msg[] = 'O campo ' . $this->getLabel() . ' é obrigatório';
            return false;
        }
        return true;
    }

    protected function filterVar()
    {

    }
}
