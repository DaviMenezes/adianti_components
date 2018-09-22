<?php
/**
 * Created by PhpStorm.
 * User: davi
 * Date: 01/12/17
 * Time: 18:33
 */

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\DEntry;
use Dvi\Adianti\Widget\Form\Field\Validator\CpfValidator;
use Dvi\Adianti\Widget\Form\Field\Validator\EmailValidator;

class DBVarchar extends DBFormField
{
    protected $size;

    public function __construct(string $name, int $size, bool $required = false, $label = null)
    {
        $this->size = $size;

        $array = explode('-', $name);
        $field_name = array_pop($array);

        $label = $label ?? $field_name;

        $this->field = new DEntry($name, $label, $size, $required);

        parent::__construct($required, $label);
    }

    #region [FACADE]
    public function setType($type)
    {
        $this->field->setType($type);
        return $this;
    }

    public function mask(string $mask)
    {
        $this->field->setMask($mask);
        return $this;
    }

    public function validateEmail()
    {
        $this->field->addValidation($this->field->getLabel(), new EmailValidator());
        return $this;
    }

    public function validateCpf()
    {
        $this->field->addValidation($this->field->getLabel(), new CPFValidator());

        $this->mask('999-999-999-99');
        return $this;
    }
    #endregion
}
