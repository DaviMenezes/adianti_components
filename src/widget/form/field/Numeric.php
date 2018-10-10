<?php

namespace Dvi\Adianti\Componente\Model\Form\Fields;

use Adianti\Base\Lib\Widget\Form\TNumeric;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;
use Dvi\Adianti\Widget\Form\Field\FormFieldValidation;
use Dvi\Adianti\Widget\Form\Field\SearchableField;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeFloat;

/**
 * Fields Numeric
 *
 * @package    Fields
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class Numeric extends TNumeric implements FormField
{
    use FormFieldTrait;
    use FormFieldValidation;
    use SearchableField;

    public function __construct($name, $decimals, $decimalsSeparator, $thousandSeparator, $required = false, string $label = null, bool $replaceOnPost = true)
    {
        parent::__construct($name, $decimals, $decimalsSeparator, $thousandSeparator, $replaceOnPost);

        $this->setup($label ?? $name, $required);

        $this->setType(new FieldTypeFloat());
    }
}
