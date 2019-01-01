<?php
namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Widget\Form\TSlider;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;

/**
 * Field Slider
 *
 * @package    Field
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 * @see https://t.me/davimenezes
 */
class Slider extends TSlider implements FormField
{
    use FormFieldTrait;
    use FormFieldValidation;
    use SearchableField;

    public function __construct(string $name, $min, $max, $step = 1, $label = null)
    {
        parent::__construct($name);

        $this->setup($label ?? $name, false, strlen($max));

        $this->setRange($min, $max, $step);
    }
}
