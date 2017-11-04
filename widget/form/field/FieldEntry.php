<?php

namespace Dvi\Widget\Form\Field;

use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TEntry;

/**
 * Field FieldEntry
 *
 * @version    Dvi 1.0
 * @package    Field
 * @subpackage form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class FieldEntry extends TEntry
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