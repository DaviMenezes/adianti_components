<?php
/**
 * Created by PhpStorm.
 * User: davi
 * Date: 01/12/17
 * Time: 18:33
 */

namespace Dvi\Adianti\Component\Model\Form\Fields;

use Adianti\Base\Lib\Validator\TCPFValidator;
use Adianti\Base\Lib\Validator\TEmailValidator;
use Adianti\Base\Lib\Widget\Form\TField;
use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\DEntry;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeString;

class DBVarchar extends DBFormField
{
    protected $size;

    public function __construct(string $name, int $size, bool $required = false, $label = null)
    {
        $this->size = $size;

        $array = explode('-', $name);
        $field_name = array_pop($array);

        $label = $label ?? $field_name;

        parent::__construct($required, $label);

        $this->field = new DEntry($name, $label, $size, $required);

        $this->setType(new FieldTypeString());
    }

    public function getField()
    {
        return $this->field;
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
        $this->field->addValidation($this->field->getLabel(), new TEmailValidator());
        return $this;
    }

    public function validateCpf()
    {
        $this->field->addValidation($this->field->getLabel(), new TCPFValidator());
        return $this;
    }
    #endregion
}
