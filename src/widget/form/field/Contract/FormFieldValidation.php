<?php

namespace Dvi\Adianti\Widget\Form\Field\Contract;

/**
 * Field FormFieldValidationInterface
 *
 * @package    Field
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
interface FormFieldValidation
{
    public function addValidations(array $array_validations);
    public function sanitize($value);
    public function validate();
    public function noValidate();
    public function setErrorValidationSession();
    public function required();
}
