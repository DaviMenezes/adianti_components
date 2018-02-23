<?php

namespace App\Adianti\Component\Model\Form\Fields;

use Adianti\Base\Lib\Validator\TRequiredValidator;
use Adianti\Base\Lib\Widget\Form\TNumeric;
use Adianti\Base\Lib\Widget\Form\TSpinner;
use Dvi\Adianti\Model\DBFormField;

/**
 * Fields FieldNumeric
 *
 * @version    Dvi 1.0
 * @package    Fields
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class FieldNumeric extends DBFormField
{
    public function __construct(string $name, $min, $max, $step, bool $required = false, string $label = null, string $type = 'numeric')
    {
        parent::__construct($name, $type, $required, $label);

        $this->field = new TSpinner($name);
        $this->field->setRange(1, 65535, 1);
        $this->field->placeholder = $this->getLabel();
    }

    public function getField()
    {
        return $this->field;
    }

    public function getLabel()
    {
        return ucfirst(parent::getLabel()?? $this->getName());
    }
}