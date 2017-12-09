<?php

namespace Dvi\Adianti\Model;

use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TDateTime;

/**
 * Model DBDateTime
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Adianti
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DBDateTime extends DBFormField
{
    public function __construct(string $name, bool $required = false, string $label = null)
    {
        parent::__construct($name, 'datetime', $required, $label);

        $this->field = new TDateTime($name);

        if ($required and $label) {
            $this->field->addValidation(ucfirst($label), new TRequiredValidator());
        }
    }

    public static function create(string $name, bool $required = false, string $label = null): DBDateTime
    {
        $field = new DBDateTime($name, $required, $label);
        return $field;
    }

    public function setMask(string $mask)
    {
        $this->field->setMask($mask);
    }
}
