<?php

namespace Dvi\Adianti\Componente\Model\Form\Fields;

use Adianti\Base\Lib\Widget\Form\TNumeric;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;
use Dvi\Adianti\Widget\Form\Field\FormFieldValidation;
use Dvi\Adianti\Widget\Form\Field\SearchableField;

/**
 * Fields DNumeric
 *
 * @package    Fields
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DNumeric extends TNumeric implements FormField
{
    use FormFieldTrait;
    use FormFieldValidation;
    use SearchableField;
}
