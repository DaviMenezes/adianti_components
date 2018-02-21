<?php

namespace Dvi\Adianti\Component\Model\Form\Fields;

use Dvi\Adianti\Model\DBFormField;
use Dvi\Adianti\Widget\Form\DText;

/**
 * Model FieldText
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Adianti
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class FieldText extends DBFormField
{
    public function __construct(string $name, string $label, int $length, int $height, bool $required = false)
    {
        parent::__construct($name, 'text', $required, $label);

        $this->field = new DText($name, $label, $length, $height, true, $required);
    }

    public static function create(
        string $name,
        int $length,
        int $height,
        bool $required = false,
        $label = null
    ):FieldText {
        return new FieldText($name, $label, $length, $height, $required);
    }

    public function getField()
    {
        return $this->field;
    }
}
