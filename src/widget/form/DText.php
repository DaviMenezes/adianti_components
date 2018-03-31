<?php

namespace Dvi\Adianti\Widget\Form;

use Adianti\Base\Lib\Validator\TMaxLengthValidator;
use Adianti\Base\Lib\Validator\TRequiredValidator;
use Adianti\Base\Lib\Widget\Form\TText;

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
class DText extends TText
{
    private $ucfirst_placeholder;
    private $field_disabled;

    public function __construct(string $name, string $placeholder = null, int $max_length = null, $height = '50', bool $tip = true, bool $required = false)
    {
        parent::__construct($name);

        $this->setLabel($placeholder);

        $this->placeholder = $placeholder;

        $this->setSize(0, $height);

        $this->ucfirst_placeholder = ucfirst($placeholder);

        if ($tip) {
            $this->setTip($this->ucfirst_placeholder);
        }

        if ($required) {
            $this->addValidation($this->ucfirst_placeholder, new TRequiredValidator());
        }

        if ($max_length) {
            $this->setMaxLength($max_length);
            $this->addValidation($this->ucfirst_placeholder, new TMaxLengthValidator(), [$max_length]);
        }
    }

    public function setMaxLength(int $length)
    {
        if ($length > 0) {
            $this->tag->maxlength = $length;
        }
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
}
