<?php

namespace Dvi\Adianti\Widget\Form\Field\Validator;

use Adianti\Base\Lib\Validator\TMaxLengthValidator;

/**
 * Validator MaxLengthValidator
 *
 * @package    Validator
 * @subpackage Field
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class MaxLengthValidator extends TMaxLengthValidator
{
    use AdiantiValidatorExtender;
}
