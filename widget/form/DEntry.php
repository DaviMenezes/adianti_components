<?php
namespace Dvi\Widget\Form;

use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TEntry;
use Dvi\Widget\Form\Field\SearchableField;

/**
 * Model DEntry
 *
 * @version    Dvi 1.0
 * @package    form
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DEntry extends TEntry
{
    use SearchableField;

    public function __construct(string $name, string $placeholder = null, bool $required = false, bool $tip = true)
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
