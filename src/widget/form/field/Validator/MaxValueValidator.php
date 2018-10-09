<?php

namespace Dvi\Adianti\Widget\Form\Field\Validator;

/**
 * Validator MaxValueValidator
 *
 * @package    Validator
 * @subpackage Field
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class MaxValueValidator extends FieldValidator
{
    protected $max_value;
    protected $default_msg;

    public function __construct($max_value, string $error_msg = null)
    {
        parent::__construct($error_msg);

        $this->max_value = $max_value;
        $this->default_msg = $msg ?? 'Valor máximo('.$max_value.') inválido';
    }

    public function validate($label, $value, $parameters = null)
    {
        if ($value > $this->max_value) {
            $this->error_msg = $this->default_msg;
            return false;
        }
        return true;
    }
}
