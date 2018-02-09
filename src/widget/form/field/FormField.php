<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Widget\Form\TField;

/**
 * Field FormField
 *
 * @version    Dvi 1.0
 * @package    Field
 * @subpackage form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class FormField extends TField
{
    public function __construct(string $name, string $placeholder = null, bool $required = false, bool $tip = true)
    {
        parent::__construct($name);


        if ($placeholder) {
            $this->placeholder = $placeholder;
        }

        if ($required) {
            $this->addValidation(ucfirst($this->placeholder), new TRequiredValidator());
        }

        if ($tip) {
            $this->setTip(ucfirst($this->placeholder));
        }
    }
}