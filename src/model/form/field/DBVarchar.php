<?php
namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\DEntry;
use Dvi\Adianti\Widget\Form\Field\Validator\CpfValidator;
use Dvi\Adianti\Widget\Form\Field\Validator\EmailValidator;

/**
 * Field DBVarchar
 *
 * @package    Field
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 */
class DBVarchar extends DBFormField
{
    public function __construct(string $name, int $size, string $label = null)
    {
        $this->field = new DEntry($name, $label, $size, false);

        parent::__construct($label);
    }

    #region [FACADE]
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

    public function validateCpf()
    {
        $this->field->addValidation($this->field->getLabel(), new CPFValidator());

        $this->mask('999-999-999-99');
        return $this;
    }
    #endregion
}
