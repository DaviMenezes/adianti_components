<?php

namespace Dvi\Adianti\Model\Fields;

use Adianti\Base\Lib\Validator\TFieldValidator;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeString;
use Dvi\Adianti\Widget\Form\Field\Validator\RequiredValidator;

/**
 * Model DBFormField
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Adianti
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class DBFormField
{
    /**@var FormField $field */
    protected $field;
    protected $label;
    protected $hide_in_edit;

    public function __construct(string $label = null)
    {
        $this->label= $label;

        if (isset($this->field)) {
            $this->field->setType(new FieldTypeString());
        }
    }

    public function getLabel()
    {
        return ucfirst(str_replace('_', ' ', $this->label));
    }

    public function label(string $label)
    {
        $this->label = $label;
        return $this;
    }

    public function getField()
    {
        return $this->field;
    }

    public function hideInEdit()
    {
        $this->hide_in_edit = true;
        return $this;
    }

    public function getHideInEdit()
    {
        return $this->hide_in_edit;
    }

    public function validation(TFieldValidator $validator)
    {
        $this->field->addValidation($this->getLabel(), $validator);
        return $this;
    }

    public function required()
    {
        $this->field->required();
        $this->validation(new RequiredValidator());
        return $this;
    }
}
