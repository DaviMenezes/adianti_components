<?php
namespace Dvi\Adianti\Widget\Form\Field\Contract;

use Adianti\Base\Lib\Widget\Form\AdiantiWidgetInterface;

/**
 * Interface for all DviFormField
 * @package    Field
 * @subpackage form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
interface FormField extends FormFieldValidation, SearchableField, AdiantiWidgetInterface
{
    public function setup(string $label, bool $required = false, int $max_length = null);
    public function setValueTest($string);
    public function disable($disable = true);
    public function isDisabled();
    public function setType(FieldTypeInterface $type);
    public function getType();
    public function label($label, string $class = null);
    public function getLabel();
    public function setReferenceName($reference_name);
    public function getReferenceName();
    public function getHideInEdit();
    public function useLabelField(bool $use = true);
}
