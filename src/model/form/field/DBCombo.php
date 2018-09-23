<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\DCombo;
use Dvi\Adianti\Widget\Form\Field\Contract\FieldTypeInterface;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeInt;

/**
 * Model DBCombo
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Component
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DBCombo extends DBFormField
{
    use DBSelectionFieldTrait;

    public function __construct(string $name, string $label = null)
    {
        $this->field = new DCombo($name, $label, false);

        parent::__construct($label);

        $this->setType(new FieldTypeInt());
    }

    public function getField()
    {
        $this->mountModelItems();

        return $this->field;
    }

    public function setType(FieldTypeInterface $type)
    {
        $this->field->setType($type);
        return $this;
    }
}
