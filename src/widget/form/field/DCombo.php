<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Widget\Form\TCombo;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;
use Dvi\Adianti\Widget\Form\Field\FormFieldValidation;
use Dvi\Adianti\Widget\Form\Field\SearchableField;
use Dvi\Adianti\Widget\Form\Field\SelectionFieldTrait;

/**
 * Model DCombo
 *
 * @version    Dvi 1.0
 * @package    form
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DCombo extends TCombo implements FormField
{
    use FormFieldTrait;
    use FormFieldValidation;
    use SearchableField;
    use SelectionFieldTrait;

    private $field_disabled;

    public function __construct(string $name, string $label = null, $required = false, array $obj_array_value = null)
    {
        parent::__construct($name);

        $this->setup($label ?? $name, $required);
        $this->tip(false);
        $this->operator('=');

        if ($obj_array_value) {
            $this->items($this->getObjItems($obj_array_value));
        }

        $this->enableSearch();
    }

    public function enableSearch()
    {
        parent::enableSearch();
        return $this;
    }

    public function disable($disable = true)
    {
        $this->field_disabled = true;

        $this->setEditable(!$disable);
    }

    public function isDisabled()
    {
        return $this->field_disabled;
    }
}


