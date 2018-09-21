<?php

namespace Dvi\Adianti\Widget\Form\Field\Validator;

/**
 * Validator MinLengthValidator
 *
 * @package    Validator
 * @subpackage Field
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class MinLengthValidator extends FieldValidator
{
    private $min_value;
    private $default_error_msg;

    public function __construct($min_value = 0, string $error_msg = null)
    {
        $this->min_value = $min_value;
        $this->default_error_msg = $error_msg ?? 'Tamanho mínimo inválido';

        parent::__construct($error_msg);
    }

    public function validate($label, $value, $parameters = null)
    {
        if (strlen($value) < $this->min_value) {
            $this->error_msg = $this->default_error_msg;
            return false;
        }
        return true;
    }
}
