<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\MultiSearch;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeInt;

/**
 * Field DBMultiSearch
 * Link between attribute table and form field MultiSearch
 * @package    Field
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 * @see https://t.me/davimenezes
 */
class DBMultiSearch extends DBFormField
{
    use DBSelectionFieldTrait;

    public function __construct($name, $min_length, $max_length, string $label = null)
    {
        $this->field = new MultiSearch($name, $min_length, $max_length, $label);

        parent::__construct($label ?? $name);

        $this->field->setType(new FieldTypeInt());
    }
}
