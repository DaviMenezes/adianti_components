<?php

namespace Dvi\Adianti\Widget\Form\Field\Validator;

use Adianti\Base\Lib\Validator\TEmailValidator;

/**
 * Validator EmailValidator
 *
 * @package    Validator
 * @subpackage Field
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class EmailValidator extends TEmailValidator
{
    use AdiantiValidatorExtender;
}
