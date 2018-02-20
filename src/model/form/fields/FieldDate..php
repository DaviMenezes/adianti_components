<?php

namespace Dvi\Adianti\Component\Model\Form\Fields;

use Adianti\Base\Lib\Validator\TRequiredValidator;
use Dvi\Adianti\Model\DBFormField;
use Dvi\Adianti\Widget\Form\DDate;

/**
 * Model DBDate
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Components
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class FieldDate extends DBFormField
{
    public function __construct(string $name, bool $required = false, string $label = null)
    {
        parent::__construct($name, 'datetime', $required, $label);
        $this->field = new DDate($name, $label);

        if ($required) {
            $this->field->addValidation($label, new TRequiredValidator());
        }
        $this->field->setDatabaseMask('yyyy-mm-dd');
    }

    public static function create(string $name, bool $required = false, string $label = null): FieldDate
    {
        $field = new FieldDate($name, $required, $label);
        return $field;
    }

}