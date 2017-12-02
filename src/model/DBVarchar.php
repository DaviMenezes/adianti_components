<?php
/**
 * Created by PhpStorm.
 * User: davi
 * Date: 01/12/17
 * Time: 18:33
 */

namespace Dvi\Adianti\Model;

use Dvi\Adianti\Widget\Form\DEntry;

class DBVarchar extends DBField
{
    private $size;
    protected $field;

    public function __construct(string $name, string $type, int $size, bool $required = false, $label = null)
    {
        $this->size = $size;

        parent::__construct($name, $type, $required, $label);

        $this->field = new DEntry($name, $label, $size, $required);
    }

    public static function create(string $name, string $type, int $size, bool $required = false, $label = null):DBVarchar
    {
        return new DBVarchar($name, $type, $size, $required, $label);
    }

    public function getFormField()
    {
        return $this->field;
    }

    public function setMask(string $mask): DEntry
    {
        $this->field->setMask($mask);
        return $this->field;
    }
}
