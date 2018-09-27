<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Util\TActionLink;
use Dvi\Adianti\Widget\Container\VBox;
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
    private $use_label_field;

    public function setup(string $label, bool $required = false, int $max_length = null)
    {
        $this->label(ucfirst($label));
        $this->required = $required;
        $this->max_length = $max_length > 0 ? $max_length : null;
        $this->tip = true;
    }

    public function useLabelField(bool $use = true)
    {
        $this->use_label_field = $use;
        return $this;
    }

    public function tip(bool $tip)
    {
        $this->tip = $tip;
    }

    public function prepare()
    {
        $this->label($this->field_label);

        $this->{'placeholder'} = strtolower($this->field_label);

        if ($this->max_length and method_exists($this, 'setMaxLength')) {
            $this->setMaxLength($this->max_length);
            $this->addValidation($this->getLabel(), new MaxLengthValidator($this->max_length));
        }

        if ($this->required) {
            $this->setProperty('required', 'required');
            $this->addValidation($this->getLabel(), new RequiredValidator());
        }

        if ($this->tip) {
            $this->setTip(parent::getLabel());
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
        $label = ucfirst($label);
        $this->setLabel($label);
        $this->field_label = $label;
        if ($class) {
            $this->label_class = $class;
        }
    }

    public function getLabel()
    {
        $label = parent::getLabel();
        $fc = mb_strtoupper(mb_substr($label, 0, 1));
        $label = $fc.mb_substr($label, 1);

        if (!empty($this->label_class)) {
            $class = ' class="dvi_str_' . $this->label_class.'"';
            $label = '<b>'.$label.'</b>';
            $label = '<span'.$class.'>'.$label.'</span>';
        }

        return  $label;
    }

    public function setReferenceName($reference_name)
    {
        $this->reference_name = $reference_name;
        return $this;
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

        $vbox = new VBox();
        if ($this->use_label_field) {
            $vbox->add($this->getLabel().$this->getValidationErrorLink());
        }
        $vbox->show();
        parent::show();
    }

    protected function getValidationErrorLink()
    {
        $link_error = null;
        if (in_array(FormFieldValidation::class, array_keys((new \ReflectionClass(self::class))->getTraits()))) {
            if ($this->error_msg) {
                $icon_error = ' <i class="fa fa-exclamation-triangle red" aria-hidden="true"></i>';
                $parameters = ['msg' => $this->getErrorValidation(), 'static' => 1];
                $link_error = new TActionLink($icon_error, new TAction([$_REQUEST['class'], 'showErrorMsg'], $parameters));
                $link_error->{'title'} = 'Clique para ver a mensagem';
            }
        }
        return $link_error;
    }
}
