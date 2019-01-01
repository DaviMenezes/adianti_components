<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Widget\Form\TSelect;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;

/**
 * Field Select
 *
 * @package    Field
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 * @see https://t.me/davimenezes
 */
class Select extends TSelect implements FormField
{
    use FormFieldTrait;
    use FormFieldValidation;
    use SearchableField;
    use SelectionFieldTrait;

    public function __construct($name, $label = null, $required = false)
    {
        parent::__construct($name);

        $this->setup($label ?? $name, $required);

        $this->operator('=');
    }

    protected function setStyle()
    {
        if ($this->size or $this->height) {
            parent::setStyle();
        }
    }
}
