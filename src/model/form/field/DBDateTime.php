<?php

namespace Dvi\Adianti\Model\Form\Field;

use Adianti\Base\Lib\Validator\TRequiredValidator;
use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\DateTime;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeString;

/**
 *  DateTime
 * Link between attribute table and form field DateTime
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Adianti
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DBDateTime extends DBFormField
{
    public function __construct(string $name, string $label = null)
    {
        $this->field = new DateTime($name);

        parent::__construct($label ?? $name);

        $this->field->setMask('dd/mm/yyyy hh:ii:ss');
        $this->field->setDatabaseMask('yyyy-mm-dd hh:ii:ss');
    }

    public function getField()
    {
        return $this->field;
    }

    public function mask(string $mask)
    {
        $this->field->setMask($mask);
        return $this;
    }

    public function setType($type)
    {
        $this->field->setType($type);
    }
}
