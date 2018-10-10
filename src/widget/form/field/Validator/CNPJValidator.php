<?php

namespace Dvi\Adianti\Widget\Form\Field\Validator;

use Adianti\Base\Lib\Validator\TCNPJValidator;

/**
 * Validator CNPJValidator
 *
 * @package    Validator
 * @subpackage Field
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class CNPJValidator extends TCNPJValidator
{
    use AdiantiValidatorExtender;
}
