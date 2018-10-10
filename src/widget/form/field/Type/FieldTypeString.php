<?php

namespace Dvi\Adianti\Widget\Form\Field\Type;

use Dvi\Adianti\Widget\Form\Field\Contract\FieldTypeInterface;

/**
 * Field FieldTypeString
 *
 * @package    Field
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class FieldTypeString implements FieldTypeInterface
{
    public function sanitize($value)
    {
        $value = filter_var($value, FILTER_SANITIZE_STRIPPED);
        $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        $value = filter_var($value, FILTER_SANITIZE_STRING);
        return $value;
    }
}
