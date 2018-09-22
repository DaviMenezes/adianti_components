<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Widget\Form\TText;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;
use Dvi\Adianti\Widget\Form\Field\FormFieldValidation;
use Dvi\Adianti\Widget\Form\Field\SearchableField;

/**
 * Model DText
 *
 * @version    Dvi 1.0
 * @package    form
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DText extends TText implements FormField
{
    use FormFieldTrait;
    use FormFieldValidation;
    use SearchableField;

    private $field_disabled;

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

}
