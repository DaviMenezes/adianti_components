<?php
namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Widget\Base\TScript;
use Adianti\Base\Lib\Widget\Form\TDate;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;
use Dvi\Adianti\Widget\Form\Field\Validator\DateValidator;

/**
 *  Date
 * @package    form
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class Date extends TDate implements FormField
{
    use FormFieldTrait;
    use FormFieldValidation;
    use SearchableField;

    protected $field_disabled;

    public function __construct($name, string $label = null, bool $required = false)
    {
        parent::__construct($name);

        $this->setup($label ?? $name, $required);
        $this->setMask('dd/mm/yyyy');
        $this->setDatabaseMask('yyyy-mm-dd');

        $this->operator('=');

        $this->addValidation($this->getLabel(), new DateValidator(), ['dd/mm/yyyy']);
    }

    public function disable($disable = true)
    {
        $this->field_disabled = $disable;

        parent::setEditable(!$disable);
    }

    public function isDisabled()
    {
        return $this->field_disabled;
    }

    public function showView()
    {
        $js_mask = str_replace('yyyy', 'yy', $this->mask);
        $language = strtolower(LANG);
        $options = json_encode($this->options);

        if (parent::getEditable())
        {
            $outer_size = 'undefined';
            if (strstr($this->size, '%') !== FALSE)
            {
                $outer_size = $this->size;
                $this->size = '100%';
            }
        }

        $data = $this->getViewData();

        $data['label'] = $this->error_msg ? 'verifique' : $this->getLabel();
        $data['field_info'] = $this->getFieldInfoValidationErrorData($this->getLabel());
        $data['editable'] = parent::getEditable();
        $data['mask'] = $this->mask ?? 'dd/mm/yyyy';
        $data['language'] = $language;
        $data['outer_size'] = $outer_size;
        $data['options'] = $options;
        $data['error_msg'] = $this->error_msg;

        view('form/fields/date', $data);
    }
}
