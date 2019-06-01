<?php
namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Core\AdiantiCoreTranslator;
use Adianti\Base\Lib\Widget\Base\TScript;
use Adianti\Base\Lib\Widget\Form\TEntry;
use Adianti\Base\Lib\Widget\Form\TForm;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;
use Exception;

/**
 * Widget Form Varchar
 * @package    form
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class Varchar extends TEntry implements FormField
{
    use FormFieldTrait;
    use FormFieldValidation;
    use SearchableField;

    public function __construct(string $name, string $label = null, int $max_length = null, bool $required = false)
    {
        parent::__construct($name);

        $this->setup($label ?? $name, $required, $max_length);

        $this->operator('like');
    }

    public function showView()
    {
        $data = $this->getViewData();

        $data['label'] = $this->error_msg ? 'verifique' : $this->getLabel();
        $data['field_info'] = $this->getFieldInfoValidationErrorData($this->getLabel());

        view("form/fields/varchar", $data);
    }

    protected function getViewData()
    {
        $data = $this->prepareViewData();

        $properties = $this->tag->getProperties();

        $data = array_merge($properties, $data);
        $data['properties'] = '';

        collect($data)->filter()->map(function ($value, $property) use (&$data) {
            $data['properties'] .= $property . '="' . $value . '" ';
        });
        return $data;
    }

    protected function prepareViewData()
    {
        $data['id'] = $this->id;
        $data['name'] = $this->name;
        if ($this->value) {
            $data['value'] = $this->value;
        }

        if (!empty($this->size)) {
            $width = strstr($this->size, '%') === false ? 'px' : '';
            $data['style'] = "width:{$this->size}$width;";
        }

        //Todo Revolver estes ifs todos com serviços

        // verify if the widget is non-editable
        if (parent::getEditable()) {
            if (isset($this->exitAction)) {
                if (!TForm::getFormByName($this->formName) instanceof TForm) {
                    throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()'));
                }
                $string_action = $this->exitAction->serialize(false);

                $this->setProperty('exitaction', "__adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback')");

                // just aggregate onBlur, if the previous one does not have return clause
                if (strstr($this->getProperty('onBlur'), 'return') == false) {
                    $this->setProperty('onBlur', $this->getProperty('exitaction'), false);
                } else {
                    $this->setProperty('onBlur', $this->getProperty('exitaction'), true);
                }
            }

            if (isset($this->exitFunction)) {
                if (strstr($this->getProperty('onBlur'), 'return') == false) {
                    $this->setProperty('onBlur', $this->exitFunction, false);
                } else {
                    $this->setProperty('onBlur', $this->exitFunction, true);
                }
            }
        } else {
            $data['readonly'] = 1;
            $data['onmouseover'] = "style.cursor='default'";
        }

        if (isset($this->completion)) {
            $options = json_encode($this->completion);
            TScript::create(" tentry_autocomplete( '{$this->id}', $options); ");
        }
        if ($this->numericMask) {
            TScript::create("tentry_numeric_mask( '{$this->id}', {$this->decimals}, '{$this->decimalsSeparator}', '{$this->thousandSeparator}'); ");
        }
        return $data;
    }
}
