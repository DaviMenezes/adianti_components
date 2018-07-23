<?php

namespace Dvi\Adianti\Widget\Form\Field\Type;

use Dvi\Adianti\Widget\Form\Field\Contract\FieldTypeInterface;

/**
 * Form FieldType
 *
 * @package    Form
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class FieldTypeUrl implements FieldTypeInterface
{
    public function sanitize($value)
    {
        $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        $value = filter_var($value, FILTER_SANITIZE_URL);
        return $value;
    }
}
