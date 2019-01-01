<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\Slider;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeInt;

/**
 * Field DBSlider
 * Link between attribute table and form field Slider
 * @package    Field
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 * @see https://t.me/davimenezes
 */
class DBSlider extends DBFormField
{
    public function __construct(string $name, $min, $max, $step, string $label = null)
    {
        $this->field = new Slider($name, $min, $max, $step, $label ?? $name);

        parent::__construct($label ?? $name);

        $this->field->setType(new FieldTypeInt());
    }
}
