<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeInt;
use Dvi\Adianti\Widget\Form\Field\UniqueSearch;

/**
 * Field DBUniqueSeach
 *
 * @package    Field
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 */
class DBUniqueSeach extends DBFormField
{
    use DBSelectionFieldTrait;

    public function __construct($name, $min_length, $max_length, string $label = null)
    {
        $this->field = new UniqueSearch($name, $min_length, $max_length, $label);

        parent::__construct($label);

        $this->field->setType(new FieldTypeInt());
    }
}
