<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\Color;

/**
 * Fields DBColor
 * Link between attribute table and form field Color
 * @package    Fields
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 * @see https://t.me/davimenezes
 */
class DBColor extends DBFormField
{
    public function __construct(string $name, int $max_length = 10, string $label = null)
    {
        $this->field = new Color($name, $label, $max_length, false);

        parent::__construct($label ?? $name);
    }
}
