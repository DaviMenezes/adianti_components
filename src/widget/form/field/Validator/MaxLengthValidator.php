<?php

namespace Dvi\Adianti\Widget\Form\Field\Validator;

/**
 * Validator MaxLengthValidator
 *
 * @package    Validator
 * @subpackage Field
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class MaxLengthValidator extends FieldValidator
{
    public function validate($label, $value, $parameters = null)
    {
        $length = $parameters[0];

        if (strlen($value) > $length) {
            if ($this->error_msg) {
                return false;
            }

            $this->error_msg = 'O campo não pode ser maior que '.$length.' caracteres.';
            return false;
        }
        return true;
    }
}
