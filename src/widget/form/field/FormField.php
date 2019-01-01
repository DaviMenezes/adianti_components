<?php

namespace Dvi\Adianti\Widget\Form\Field;

use App\Http\Request;
use Dvi\Adianti\Widget\Container\VBox;
use Dvi\Adianti\Widget\Form\Field\Contract\FieldTypeInterface;
use Dvi\Adianti\Widget\Form\Field\Validator\MaxLengthValidator;
use Dvi\Adianti\Widget\Form\Field\Validator\RequiredValidator;
use Dvi\Adianti\Widget\Util\Action;
use Dvi\Adianti\Widget\Util\ActionLink;

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
    /**@var \App\Http\Request*/
    protected $request;
    protected $field_disabled;
    /**@var FieldTypeInterface */
    protected $type;
    protected $error_msg = array();
    protected $required;
    protected $label_class;
    protected $base_class_name;
    protected $reference_name;
    protected $tip;
    protected $max_length;
    protected $field_label;
    protected $use_label_field;

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

        if (!empty($this->field_label)) {
            $this->{'placeholder'} = strtolower($this->field_label);
        }

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
        $label = $this->wrapperStringClass(parent::getLabel());

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
        try {
            $this->prepare();

            if (!$this->use_label_field) {
                $this->showField();
                return;
            }

            $vbox = new VBox();

            if ($this->error_msg) {
                $this->showField();

                $label =  $this->wrapperStringClass('verifique');
                $vbox->add($this->getValidationErrorLink($label));
                $vbox->show();
                return;
            }
            $vbox->add($this->getLabel());
            $vbox->show();
            $this->showField();
        } catch (\Exception $e) {
            throw new \Exception('Houve um problema na construção do campo '. $this->getName());
        }
    }

    protected function getValidationErrorLink(string $label = null)
    {
        $link_error = null;
        if (in_array(FormFieldValidation::class, array_keys((new \ReflectionClass(self::class))->getTraits()))) {
            if ($this->error_msg) {
                $this->setErrorValidationSession();
                $parameters = ['field' => $this->getName(), 'form' => $this->getFormName(), 'static' => 1];

                $route_base = Request::instance()->attr('route_base');
                $link_error = new ActionLink(new Action(urlRoute($route_base.'/show_error'), 'GET', $parameters));
                $link_error->label($label);
                $link_error->{'title'} = 'Clique para ver a mensagem';
                $link_error->icon('fa:exclamation-triangle red', 'padding-left: 2px;');
            }
        }
        return $link_error;
    }

    /**
     * @param $label
     * @return string
     */
    protected function wrapperStringClass($label): string
    {
        $fc = mb_strtoupper(mb_substr($label, 0, 1));
        $label = $fc . mb_substr($label, 1);

        if (!empty($this->label_class)) {
            $class = ' class="dvi_str_' . $this->label_class . '"';
            $label = '<b>' . $label . '</b>';
            $label = '<span' . $class . '>' . $label . '</span>';
        }
        return $label;
    }

    protected function showField()
    {
        if (method_exists($this, 'showView')) {
            $this->showView();
            return;
        }
        parent::show();
    }
}
