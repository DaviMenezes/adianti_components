<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\Date;
use Dvi\Adianti\Widget\Form\Field\DateTime;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeString;

/**
 *  Date
 * Link between attribute table and form field Date
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Components
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DBDate extends DBFormField
{
    public function __construct(string $name, string $label = null)
    {
        $this->field = new Date($name, $label);

        parent::__construct($label ?? $name);

        $this->setType(new FieldTypeString());
    }

    public function getField()
    {
        return $this->field;
    }

    public function setType($type)
    {
        $this->field->setType($type);
    }
}
