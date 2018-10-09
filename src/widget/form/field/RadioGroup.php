<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Widget\Form\TRadioGroup;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;

/**
 * RadioGroup
 * @package    Form
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class RadioGroup extends TRadioGroup implements FormField
{
    use FormFieldTrait;
    use FormFieldValidation;
    use SearchableField;

    protected $field_disabled;

    public function __construct(string $name, $label, $required = false)
    {
        parent::__construct($name);

        $this->setup($label ?? $name, $required);

        $this->setLayout('horizontal');
        $this->setUseButton();

        $this->operator('=');
    }

    public function items(array $items)
    {
        $this->addItems($items);
    }

    public function disable($disable = true)
    {
        $this->field_disabled = $disable;

        $this->setEditable(!$disable);
    }

    public function isDisabled()
    {
        return $this->field_disabled;
    }
}
