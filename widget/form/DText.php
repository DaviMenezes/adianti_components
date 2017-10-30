<?php

namespace Dvi\Widget\Form;

use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TText;

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
    public function __construct(string $name, string $placeholder = null, $height = '50', bool $tip = true, bool $required = false)
    {
        parent::__construct($name);

        if ($placeholder) {
            $this->placeholder = $placeholder;
        }

        $this->setSize(0, $height);

        if ($tip) {
            $this->setTip(ucfirst($placeholder));
        }

        if ($required) {
            $this->addValidation(ucfirst($placeholder), new TRequiredValidator());
        }
    }
}