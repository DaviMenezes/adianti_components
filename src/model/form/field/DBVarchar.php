<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\Validator\CpfValidator;
use Dvi\Adianti\Widget\Form\Field\Validator\EmailValidator;
use Dvi\Adianti\Widget\Form\Field\Varchar;

/**
 * Field DBVarchar
 * Link between attribute table and form field Varchar
 * @package    Field
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 * @see https://t.me/davimenezes
 */
class DBVarchar extends DBFormField
{
    public function __construct(string $name, int $size, string $label = null)
    {
        $this->field = new Varchar($name, $label, $size, false);

        parent::__construct($label ?? $name);
    }

    #region [FACADE] Especific methods to this class
    public function setType($type)
    {
        $this->field->setType($type);
        return $this;
    }

    public function mask(string $mask)
    {
        $this->field->setMask($mask);
        return $this;
    }

    public function validateEmail()
    {
        $this->field->addValidation($this->field->getLabel(), new EmailValidator());
        return $this;
    }

    public function validateCpf($debug = true)
    {
        $this->field->addValidation($this->field->getLabel(), new CPFValidator($debug));

        $this->mask('999.999.999-99');
        return $this;
    }
    #endregion
}
