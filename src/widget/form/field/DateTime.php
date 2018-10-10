<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\App\Lib\Validator\TDateValidator;
use Adianti\Base\Lib\Widget\Form\TDateTime;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;

/**
 * Fields DateTime
 *
 * @package    Fields
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DateTime extends TDateTime implements FormField
{
    use FormFieldTrait;
    use FormFieldValidation;
    use SearchableField;

    public function __construct($name, $label = null, $required = false)
    {
        parent::__construct($name);

        $this->setup($label ?? $name, $required);
        $this->setMask('dd/mm/yyyy H:i:s');
        $this->setDatabaseMask('yyyy-mm-dd H:i:s');
        $this->addValidation($this->getLabel(), new TDateValidator());
        $this->operator('=');
    }
}
