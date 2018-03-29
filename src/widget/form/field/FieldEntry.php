<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Validator\TMaxLengthValidator;
use Adianti\Base\Lib\Validator\TRequiredValidator;
use Adianti\Base\Lib\Widget\Form\TEntry;

/**
 * Field FieldEntry
 *
 * @version    Dvi 1.0
 * @package    Field
 * @subpackage form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class FieldEntry extends TEntry
{
    private $ucfirstLabel;

    public function __construct(string $name, string $placeholder = null, int $max_length = null, bool $required = false, bool $tip = true)
    {
        parent::__construct($name);

        $label = str_replace('_', ' ', $placeholder, $count);

        $this->setLabel($label);

        if ($placeholder) {
            $this->placeholder = $label;
        }

        $this->ucfirstLabel = ucfirst($label);

        if ($max_length) {
            $this->setMaxLength($max_length);
            $this->addValidation($this->ucfirstLabel, new TMaxLengthValidator(), [$max_length]);

            $this->placeholder = $label. ' max length. '.$max_length;
        }

        if ($required) {
            $this->addValidation($this->ucfirstLabel, new TRequiredValidator());
        }

        if ($tip) {
            $this->setTip($this->ucfirstLabel);
        }
    }

    public function addValidations(array $array_validations)
    {
        foreach ($array_validations as $validation) {
            $this->addValidation($this->ucfirstLabel, $validation);
        }
    }

    public function setValueTest($string)
    {
        parent::setValue($string);
    }
}
