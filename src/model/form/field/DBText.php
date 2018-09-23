<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\DText;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeString;

/**
 * Model FieldText
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Adianti
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DBText extends DBFormField
{
    public function __construct(string $name, int $maxlength, int $height, string $label = null)
    {
        $this->field = new DText($name, $label, $maxlength, $height);

        parent::__construct($label);
    }

    /**@return DText*/
    public function getField()
    {
        return $this->field;
    }

    public function setType($type)
    {
        $this->field->setType($type);
    }
}
