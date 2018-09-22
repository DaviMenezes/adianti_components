<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\Color;

/**
 * Fields DBColor
 *
 * @package    Fields
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 */
class DBColor extends DBFormField
{
    public function __construct(string $name, int $max_length = 10, bool $required = false, string $label = null)
    {
        $this->field = new Color($name, $label, $max_length, $required);

        parent::__construct($required, $label);
    }
}
