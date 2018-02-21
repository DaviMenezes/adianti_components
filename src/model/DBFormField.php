<?php

namespace Dvi\Adianti\Model;

use Adianti\Base\Lib\Validator\TRequiredValidator;
use Adianti\Base\Lib\Widget\Form\TEntry;

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
    protected $field;
    protected $form_field_class;
    private $label;

    public function __construct(string $name, string $type, bool $required = false, string $label = null)
    {
        parent::__construct($name, $type, $required);

        $this->label= $label ?? $name;
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

    abstract public function getField();
}
