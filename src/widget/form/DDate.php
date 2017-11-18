<?php
namespace Dvi\Adianti\Widget\Form;

use Adianti\Widget\Form\TDate;

/**
 * Model DDate
 *
 * @version    Dvi 1.0
 * @package    form
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DDate extends TDate
{
    public function __construct($name, string $placeholder = null, bool $required = false, bool $tip = true)
    {
        parent::__construct($name);

        if ($placeholder) {
            $this->placeholder = $placeholder;
        }

        if ($required) {
            $this->addValidation(ucfirst($this->placeholder), new TRequiredValidator());
        }

        if ($tip) {
            $this->setTip(ucfirst($this->placeholder));
        }
    }
}
