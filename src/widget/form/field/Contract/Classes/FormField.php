<?php

namespace Dvi\Adianti\Widget\Form\Field\Contract\Classes;

use Adianti\Base\Lib\Widget\Form\TField;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField as IFormField;
use Dvi\Adianti\Widget\Form\Field\Contract\FormFieldValidation;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;
use Dvi\Adianti\Widget\Form\Field\FormFieldValidation as FormFieldValidationTrait;

/**
 * Contract FormField
 *
 * @package    Contract
 * @subpackage Field
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait FormField
{
    use \Dvi\Adianti\Widget\Form\Field\FormField;

    public function getChildField()
    {
        return $this->field;
    }
}
