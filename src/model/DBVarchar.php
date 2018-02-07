<?php
/**
 * Created by PhpStorm.
 * User: davi
 * Date: 01/12/17
 * Time: 18:33
 */

namespace Dvi\Adianti\Model;

use Dvi\Adianti\Widget\Form\DEntry;

class DBVarchar extends DBFormField
{
    private $size;

    public function __construct(string $name, string $type, int $size, bool $required = false, $label = null)
    {
        $this->size = $size;

        parent::__construct($name, $type, $required, $label);

        $placeholder = $label ?? $name;
        $this->field = new DEntry($name, $placeholder, $size, $required);
    }

    public static function create(string $name, string $type, int $size, bool $required = false, $label = null):DBVarchar
    {
        return new DBVarchar($name, $type, $size, $required, $label);
    }
}
