<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Core\AdiantiCoreTranslator;
use Adianti\Base\Lib\Widget\Form\TForm;
use Adianti\Base\Lib\Widget\Form\TText;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;
use Exception;

/**
 *  Text
 * @package    form
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class Text extends TText implements FormField
{
    use FormFieldTrait;
    use FormFieldValidation;
    use SearchableField;

    protected $field_disabled;

    public function __construct(string $name, string $label = null, int $max_length = null, $height = '50')
    {
        parent::__construct($name);

        $this->setup($label ?? $name, false, $max_length);

        $this->setSize(0, $height);
    }

    public function setMaxLength(int $length)
    {
        if ($length > 0) {
            $this->setProperty('maxlength', $length);
        }
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

    public function showView()
    {
        $this->tag->{'name'}  = $this->name;   // tag name

        if ($this->size) {
            $size = (strstr($this->size, '%') !== false) ? $this->size : "{$this->size}px";
            $this->setProperty('style', "width:{$size};", false); //aggregate style info
        }

        if ($this->height) {
            $height = (strstr($this->height, '%') !== false) ? $this->height : "{$this->height}px";
            $this->setProperty('style', "height:{$height}", false); //aggregate style info
        }

        if ($this->id and empty($this->tag->{'id'})) {
            $this->tag->{'id'} = $this->id;
        }

        // check if the field is not editable
        if (!parent::getEditable()) {
            // make the widget read-only
            $this->tag->{'readonly'} = "1";
            $this->tag->{'class'} = $this->tag->{'class'} == 'tfield' ? 'tfield_disabled' : $this->tag->{'class'} . ' tfield_disabled'; // CSS
        }

        if (isset($this->exitAction)) {
            if (!TForm::getFormByName($this->formName) instanceof TForm) {
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()'));
            }
            $string_action = $this->exitAction->serialize(false);
            $this->setProperty('exitaction', "__adianti_post_lookup('{$this->formName}', '{$string_action}', this, 'callback')");
            $this->setProperty('onBlur', $this->getProperty('exitaction'), false);
        }

        if (isset($this->exitFunction)) {
            $this->setProperty('onBlur', $this->exitFunction, false);
        }

        // add the content to the textarea
        $this->tag->add(htmlspecialchars($this->value));

        $properties = '';
        collect($this->tag->getProperties())
            ->filter()
            ->map(function ($value, $property) use (&$properties) {
                $properties .= $property.'='. $value.' ';
            });

        $params = [
            'properties' => $properties,
            'value' => $this->value ?? null,
            'label' => $this->error_msg ? $this->wrapperStringClass('verifique') : $this->getLabel(),
            'field_info' => $this->getFieldInfoValidationErrorData($this->getLabel())
        ];

        view('form/fields/text', $params);
    }
}
