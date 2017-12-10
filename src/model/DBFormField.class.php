<?php

namespace Dvi\Adianti\Model;

use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TEntry;

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
abstract class DBFormField extends DBField
{
    /**@var TEntry $field*/
    protected $field;
    protected $form_field_class;
    private $label;

    public function __construct(string $name, string $type, bool $required = false, string $label = null)
    {
        parent::__construct($name, $type, $required);

        $this->label= $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function mask(string $mask)
    {
        $this->field->setMask($mask);
        return $this;
    }

    public function type(string $class)
    {
        $this->form_field_class  = $class;

        return $this;
    }

    public function getFormField()
    {
        return $this->field;
    }
}
