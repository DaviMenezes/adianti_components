<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Core\AdiantiCoreTranslator;
use Adianti\Base\Lib\Widget\Form\TCombo;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;

/**
 *  Combo
 *
 * @version    Dvi 1.0
 * @package    form
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class Combo extends TCombo implements FormField
{
    use FormFieldTrait;
    use FormFieldValidation;
    use SearchableField;
    use SelectionFieldTrait;

    protected $field_disabled;

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
        $this->searchable = true;
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

    protected function getTextPlaceholder()
    {
        $placeholder =  strtolower(AdiantiCoreTranslator::translate('Select') . ' '. $this->field_label);
        if ($this->isRequired()) {
            $placeholder = '<span style="color: #d9534f">'.$placeholder.'</span>';
        }
        return $placeholder;
    }
}


