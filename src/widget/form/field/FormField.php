<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Validator\TMaxLengthValidator;
use Adianti\Base\Lib\Validator\TRequiredValidator;
use Dvi\Adianti\Widget\Form\Field\Contract\FieldTypeInterface;
use Dvi\Adianti\Widget\Form\Field\Validator\MaxLengthValidator;
use Dvi\Adianti\Widget\Form\Field\Validator\RequiredValidator;

/**
 * Field DField
 *
 * @package    Field
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait FormField
{
    private $ucfirst_label;
    private $field_disabled;
    /**@var FieldTypeInterface $type*/
    private $type;
    protected $error_msg = array();
    protected $required;
    protected $label_class;
    protected $base_class_name;
    private $reference_name;

    public function prepare(string $placeholder = null, bool $required = false, bool $tip = true, int $max_length = null)
    {
        $label = str_replace('_', ' ', $placeholder);

        $this->required = $required;

        $this->setFieldLabel($label);

        if ($placeholder) {
            $this->placeholder = $label;
        }

        $this->ucfirst_label = ucfirst($label);

        if (method_exists($this, 'setMaxLength') and $max_length) {
            $this->setMaxLength($max_length);
            $this->addValidation($this->ucfirst_label, new MaxLengthValidator(), [$max_length]);
        }

        if ($required) {
            $this->addValidation($this->ucfirst_label, new RequiredValidator());
        }

        if ($tip) {
            $this->setTip($this->ucfirst_label);
        }
    }

    public function setValueTest($string)
    {
        parent::setValue($string);
    }

    public function disable($disable = true)
    {
        $this->field_disabled = $disable;

        if ($disable) {
            $this->class = 'tfield_disabled';
            $this->readonly = '1';
        }
    }

    public function isDisabled()
    {
        return $this->field_disabled;
    }

    public function setType(FieldTypeInterface $type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setFieldLabel($label, string $class = null)
    {
        $this->setLabel($label);

        $this->label_class = $class;
    }

    public function getLabel()
    {
        $label = parent::getLabel();
        $fc = mb_strtoupper(mb_substr($label, 0, 1));
        $label = $fc.mb_substr($label, 1);

        if (!empty($this->label_class)) {
            $class = ' class="dvi_str_' . $this->label_class.'"';
            $label = '<b>'.$label.'</b>';
            return '<span'.$class.'>'.$label.'</span>';
        }

        return  $label;
    }

    public function setReferenceName($reference_name)
    {
        $this->reference_name = $reference_name;
    }

    public function getReferenceName()
    {
        return $this->reference_name;
    }

    public function getHideInEdit()
    {
        return $this->hide_in_edit;
    }

    public function setValue($value)
    {
        if (empty($value)) {
            return;
        }
        if (method_exists($this, 'sanitize')) {
            $value = $this->sanitize($value);
        }
        parent::setValue($value);
    }
}
