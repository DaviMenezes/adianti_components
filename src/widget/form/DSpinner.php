<?php

namespace Dvi\Adianti\Widget\Form;

use Adianti\Base\Lib\Widget\Form\TSpinner;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;
use Dvi\Adianti\Widget\Form\Field\FormFieldValidation;
use Dvi\Adianti\Widget\Form\Field\SearchableField;

/**
 * Form DSpinner
 *
 * @package    Form
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DSpinner extends TSpinner implements FormField
{
    use FormFieldTrait;
    use FormFieldValidation;
    use SearchableField;

    public function __construct(
        string $name,
        int $min,
        int $max,
        int $step,
        bool $required = false,
        bool $tip = true
    ) {
        parent::__construct($name);

        $this->prepare($name, $required, $tip, strlen($max));

        $this->operator('=');

        $this->setRange($min, $max, $step);
    }
}
