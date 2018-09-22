<?php

namespace Dvi\Adianti\Widget\Form\Field;

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
    private $field_disabled;
    /**@var FieldTypeInterface $type*/
    private $type;
    protected $error_msg = array();
    protected $required;
    protected $label_class;
    protected $base_class_name;
    private $reference_name;
    private $tip;
    private $max_length;
    private $field_label;

    public function setup(string $label, bool $required = false, int $max_length = null)
    {
        $this->field_label = $label;
        $this->required = $required;
        $this->max_length = $max_length > 0 ? $max_length : null;
        $this->tip = true;
    }

    public function tip(bool $tip)
    {
        $this->tip = $tip;
    }

    public function prepare(string $placeholder = null, bool $required = false, bool $tip = true, int $max_length = null)
    {
        $this->label(ucfirst($this->field_label));

        $this->{'placeholder'} = strtolower($this->field_label);

        if ($this->max_length and method_exists($this, 'setMaxLength')) {
            $this->setMaxLength($this->max_length);
            $this->addValidation($this->getLabel(), new MaxLengthValidator(), [$this->max_length]);
        }

        if ($this->required) {
            $this->addValidation($this->getLabel(), new RequiredValidator());
        }

        if ($this->tip) {
            $this->setTip($this->getLabel());
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

    public function label($label, string $class = null)
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

    public function show()
    {
        $this->prepare();

        parent::show();
    }
}
