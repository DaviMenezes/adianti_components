<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Core\AdiantiCoreTranslator;
use Adianti\Base\Lib\Widget\Base\TScript;
use Adianti\Base\Lib\Widget\Form\TCombo;
use Adianti\Base\Lib\Widget\Form\TForm;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;
use Exception;

/**
 *  Combo
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

    public function showView()
    {
        // define the tag properties
        $data['name']  = $this->name;    // tag name

        if ($this->id and empty($this->tag->{'id'})) {
            $data['id'] = $this->id;
        }

        if (!empty($this->size)) {
            if (strstr($this->size, '%') !== false) {
                $data['style'] = "width:{$this->size};"; //aggregate style info
            } else {
                $data['style'] = "width:{$this->size}px;"; //aggregate style info
            }
        }

        if (isset($this->changeAction)) {
            if (!TForm::getFormByName($this->formName) instanceof TForm) {
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()'));
            }

            $string_action = $this->changeAction->serialize(false);
            $data['changeaction'] = "__adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback')";
            $data['onChange'] = $this->getProperty('changeaction');
        }

        if (isset($this->changeFunction)) {
            $data['changeaction'] = $this->changeFunction;
            $data['onChange'] = $this->changeFunction;
        }

        // verify whether the widget is editable
        if (!parent::getEditable()) {
            // make the widget read-only
            $data['onclick']  = "return false;";
            $data['style']   .= ';pointer-events:none';
            $data['tabindex'] = '-1';
            $data['class']    = 'tcombo_disabled'; // CSS
        }

        if ($this->searchable) {
            $data['role'] = 'tcombosearch';
        }

        // shows the combobox
//        $this->renderItems();

        $params = [
            'label' => $this->error_msg ? $this->wrapperStringClass('verifique') : $this->getLabel(),
            'field_info' => $this->getFieldInfoValidationErrorData($this->getLabel()),
            'properties' => $data,
            'option_items' => $this->items,
            'options_properties' => []
        ];

        view('form/fields/combo', $params);

        if ($this->searchable) {
            $select = $this->getTextPlaceholder();
            TScript::create("tcombo_enable_search('#{$this->id}', '{$select}')");

            if (!parent::getEditable()) {
                TScript::create(" tmultisearch_disable_field( '{$this->formName}', '{$this->name}'); ");
            }
        }
    }
}
