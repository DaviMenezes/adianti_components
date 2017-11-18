<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Validator\TMaxLengthValidator;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TEntry;

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
    private $ucfirstPlaceholder;

    public function __construct(string $name, string $placeholder = null, int $maxlength = null, bool $required = false, bool $tip = true)
    {
        parent::__construct($name);

        if ($placeholder) {
            $this->placeholder = $placeholder;
        }

        if ($maxlength) {
            $this->setMaxLength($maxlength);
            $this->addValidation($this->ucfirstPlaceholder, new TMaxLengthValidator(), [$maxlength]);
        }

        if ($required) {
            $this->addValidation(ucfirst($this->placeholder), new TRequiredValidator());
        }

        if ($tip) {
            $this->setTip(ucfirst($this->placeholder));
        }
    }

    public function addValidations(array $array_validations)
    {
        foreach ($array_validations as $validation) {
            $this->addValidation($this->ucfirstPlaceholder, $validation);
        }
    }

    public function setValueTest($string)
    {
        parent::setValue($string);
    }
}