<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\Select;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeInt;

/**
 * Field DBSelect
 *
 * @package    Field
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 */
class DBSelect extends DBFormField
{
    use DBSelectionFieldTrait;

    public function __construct(string $name, string $label = null, bool $required = false)
    {
        $this->field = new Select($name, $label, $required);

        parent::__construct($required, $label);

        $this->field->setType(new FieldTypeInt());
    }

    public function getField():Select
    {
        return parent::getField();
    }
}
