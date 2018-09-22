<?php

namespace App\Adianti\Model\Form\Fields;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\DPassword;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeString;

/**
 * Fields DBPassword
 *
 * @package    Fields
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 */
class DBPassword extends DBFormField
{
    public function __construct(string $name, string $max_length, string $label = null)
    {
        parent::__construct(false, $label ?? $name);

        $this->field = new DPassword($name, $max_length, strtolower($label) ?? 'password');

        $this->field->setType(new FieldTypeString());
    }
}
