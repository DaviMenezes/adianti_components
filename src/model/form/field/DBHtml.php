<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\HtmlEditor;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeHtml;

/**
 * Fields FieldHtml
 * Link between attribute table and form field HtmlEditor
 * @version    Dvi 1.0
 * @package    Fields
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DBHtml extends DBFormField
{
    public function __construct(string $name, int $height, string $label = null)
    {
        $this->field = new HtmlEditor($name, $height, $label);

        parent::__construct($label ?? $name);

        $this->setType(new FieldTypeHtml());
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
