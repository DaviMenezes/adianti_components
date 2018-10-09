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
    protected $max_length;
    protected $default_msg;

    public function __construct($max_value, string $error_msg = null)
    {
        parent::__construct($error_msg);

        $this->max_length = $max_value;
        $this->default_msg = $msg ?? 'Tamanho máximo('.$max_value.') inválido';
    }

    public function validate($label, $value, $parameters = null)
    {
        if (strlen($value) > $this->max_length) {
            $this->error_msg = $this->default_msg;
            return false;
        }
        return true;
    }
}
