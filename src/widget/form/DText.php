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
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DText extends TText
{
    private $ucfirst_placeholder;

    public function __construct(string $name, string $placeholder = null, int $maxlength = null, $height = '50', bool $tip = true, bool $required = false)
    {
        parent::__construct($name);

        if ($placeholder) {
            $this->placeholder = $placeholder;
        }

        $this->setSize(0, $height);

        $this->ucfirst_placeholder = ucfirst($placeholder);

        if ($tip) {
            $this->setTip($this->ucfirst_placeholder);
        }

        if ($required) {
            $this->addValidation($this->ucfirst_placeholder, new TRequiredValidator());
        }

        if ($maxlength) {
            $this->setMaxLength($maxlength);
            $this->addValidation($this->ucfirst_placeholder, new TMaxLengthValidator(), [$maxlength]);
        }
    }

    public function setMaxLength(int $length)
    {
        if ($length > 0) {
            $this->tag->maxlength = $length;
        }
    }
}
