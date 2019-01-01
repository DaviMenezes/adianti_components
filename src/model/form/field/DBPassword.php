<?php

namespace App\Adianti\Model\Form\Fields;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\Password;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeString;

/**
 * Fields DBPassword
 * Link between attribute table and form field Password
 * @package    Fields
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 * @see https://t.me/davimenezes
 */
class DBPassword extends DBFormField
{
    public function __construct(string $name, string $max_length, string $label = null)
    {
        $this->field = new Password($name, $max_length, strtolower($label) ?? 'password');

        parent::__construct($label ?? $name);
    }
}
