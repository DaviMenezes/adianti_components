<?php

namespace Dvi\Adianti\Widget\Form\Field\Validator;

/**
 * Validator AdiantiValidatorExtender
 *
 * @package    Validator
 * @subpackage Field
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait AdiantiValidatorExtender
{
    protected $error_msg;
    protected $error_msg_default;

    public function __construct(string $error_msg = null)
    {
        $this->error_msg = $error_msg;
    }

    public function getErrorMsg()
    {
        return $this->error_msg;
    }

    public function validate($label, $value, $parameters = null)
    {
        try {
            parent::validate($label, $value, $parameters);
            return true;
        } catch (\Exception $e) {
            $this->error_msg = $this->error_msg ?? $this->error_msg_default;
            return false;
        }
    }
}
