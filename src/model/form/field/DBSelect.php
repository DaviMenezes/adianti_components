<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\Select;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeInt;

/**
 * Field DBSelect
 * Link between attribute table and form field Select
 * @package    Field
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 * @see https://t.me/davimenezes
 */
class DBSelect extends DBFormField
{
    use DBSelectionFieldTrait;

    public function __construct(string $name, string $label = null)
    {
        $this->field = new Select($name, $label);

        parent::__construct($label ?? $name);

        $this->field->setType(new FieldTypeInt());
    }

    public function getField():Select
    {
        return parent::getField();
    }
}
