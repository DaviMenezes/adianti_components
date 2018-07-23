<?php
namespace Dvi\Adianti\Widget\Form\Field\Contract;

/**
 * Interface for all DviFormField
 *
 * @version    Dvi 1.0
 * @package    Field
 * @subpackage form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
interface FormField extends FormFieldValidation, SearchableField
{
    public function prepare(string $placeholder = null, bool $required = false, bool $tip = true, int $max_length = null);
    public function setValueTest($string);
    public function disable($disable = true);
    public function isDisabled();
    public function setType(FieldTypeInterface $type);
    public function getType();
    public function setFieldLabel($label, string $class = null);
    public function getLabel();
    public function setReferenceName($reference_name);
    public function getReferenceName();
    public function getHideInEdit();
}
