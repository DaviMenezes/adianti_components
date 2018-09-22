<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Widget\Form\THidden;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeString;

/**
 * Form DHidden
 *
 * @version    Dvi 1.0
 * @package    Form
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DHidden extends THidden
{
    use FormField;

    public function __construct(string $name, $default_value = null)
    {
        parent::__construct($name);

        if ($default_value) {
            $this->setValue($default_value);
        }

        $this->setType(new FieldTypeString());
    }
}
