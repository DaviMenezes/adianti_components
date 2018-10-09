<?php

namespace Dvi\Adianti\Widget\Form\Field\Validator;

/**
 * Validator MinValueValidator
 *
 * @package    Validator
 * @subpackage Field
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class MinValueValidator extends FieldValidator
{
    protected $min_value;
    protected $default_error_msg;

    public function __construct($min_value = 0, string $error_msg = null)
    {
        $this->min_value = $min_value;
        $this->default_error_msg = $error_msg ?? 'Valor mínimo ('.$min_value.') inválido';

        parent::__construct($error_msg);
    }

    public function validate($label, $value, $parameters = null)
    {
        if ($value < $this->min_value) {
            $this->error_msg = $this->default_error_msg;
            return false;
        }
        return true;
    }
}
