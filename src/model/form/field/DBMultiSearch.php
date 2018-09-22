<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\MultiSearch;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeInt;

/**
 * Field DBMultiSearch
 *
 * @package    Field
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 */
class DBMultiSearch extends DBFormField
{
    use DBSelectionFieldTrait;

    public function __construct($name, $min_length, $max_length, bool $required = false, string $label = null)
    {
        $this->field = new MultiSearch($name, $min_length, $max_length, $label);

        parent::__construct($required, $label);

        $this->field->setType(new FieldTypeInt());
    }
}
