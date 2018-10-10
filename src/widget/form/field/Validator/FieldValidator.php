<?php

namespace Dvi\Adianti\Widget\Form\Field\Validator;

use Adianti\Base\Lib\Validator\TFieldValidator;

/**
 * Validator FieldValidator
 *
 * @package    Validator
 * @subpackage Field
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class FieldValidator extends TFieldValidator
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
}
