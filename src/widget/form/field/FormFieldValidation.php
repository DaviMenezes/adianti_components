<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Dvi\Adianti\Widget\Form\Field\Validator\FieldValidator;

/**
 * Field ValidationTrait
 *
 * @package    Field
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait FormFieldValidation
{
    public function addValidations(array $array_validations)
    {
        foreach ($array_validations as $validation) {
            $this->addValidation($this->getLabel(), $validation);
        }
    }

    public function sanitize($value)
    {
        if (empty($value)) {
            return null;
        }
        if (empty($this->type)) {
            return $value;
        }
        return $this->type->sanitize($value);
    }

    public function validating()
    {
        $this->validate();

        if (count($this->error_msg)) {
            $this->label_class = 'danger';
        }
        return count($this->error_msg) ? false : true;
    }

    public function validate()
    {
        if ($this->getValidations()) {
            foreach ($this->getValidations() as $validation) {
                $label = $validation[0];
                $validator = $validation[1];
                $parameters = $validation[2];

                /**@var FieldValidator $validator */
                if (!$validator->validate($label, $this->getValue(), $parameters)) {
                    $this->addErrorMessage($validator->getErrorMsg());
                }
            }
        }
    }

    public function getErrorValidation()
    {
        $msg_errors = false;
        foreach ($this->error_msg as $key => $item) {
            if ($key == 0) {
                $msg_errors .= 'Campo: <b>'. $this->getLabel(). '</b><br>';
            }
            $msg_errors .= $item;
            if ($key + 1 < count($this->error_msg)) {
                $msg_errors .= '<br>';
            }
        }

        return $msg_errors;
    }

    public function required()
    {
        $this->required = true;
        return $this;
    }

    public function isRequired()
    {
        return $this->required;
    }

    public function addErrorMessage($msg)
    {
        $this->error_msg[] = $msg;
    }
}
