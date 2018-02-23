<?php
/**
 * Created by PhpStorm.
 * User: davi
 * Date: 01/12/17
 * Time: 18:33
 */

namespace Dvi\Adianti\Component\Model\Form\Fields;

use Adianti\Base\Lib\Validator\TEmailValidator;
use Dvi\Adianti\Model\DBFormField;
use Dvi\Adianti\Widget\Form\DEntry;

class FieldVarchar extends DBFormField
{
    private $size;

    public function __construct(string $name, string $type, int $size, bool $required = false, $label = null)
    {
        $this->size = $size;

        parent::__construct($name, $type, $required, $label);

        $placeholder = $label ?? $name;
        $this->field = new DEntry($name, $placeholder, $size, $required);
    }

    public static function create(
        string $name,
        string $type,
        int $size,
        bool $required = false,
        $label = null
    ):FieldVarchar {
        return new FieldVarchar($name, $type, $size, $required, $label);
    }

    public function getField(): DEntry
    {
        return $this->field;
    }

    public function mask(string $mask)
    {
        $this->field->setMask($mask);
        return $this;
    }

    public function validateEmail()
    {
        $this->field->addValidation($this->field->getLabel(), new TEmailValidator());
    }
}
