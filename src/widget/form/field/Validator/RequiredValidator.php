<?php

namespace Dvi\Adianti\Widget\Form\Field\Validator;

/**
 * Validator RequiredValidator
 *
 * @package    Validator
 * @subpackage Field
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class RequiredValidator extends FieldValidator
{
    public function __construct(string $error_msg = null)
    {
        parent::__construct($error_msg);

        $this->error_msg_default = 'Campo obrigatório';
    }

    public function validate($label, $value, $parameters = null):bool
    {
        if ((is_null($value))
            or (is_scalar($value) and !is_bool($value) and trim($value)=='')
            or (is_array($value) and count($value)==1 and isset($value[0]) and empty($value[0]))
            or (is_array($value) and empty($value))) {
            $this->error_msg = $this->error_msg ?? $this->error_msg_default;
            return false;
        }
        return true;
    }
}
