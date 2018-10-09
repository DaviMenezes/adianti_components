<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\Text;
use Dvi\Adianti\Widget\Form\Field\Type\FieldTypeString;

/**
 *  FieldText
 * Link between attribute table and form field Text
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
        $this->field = new Text($name, $label, $maxlength, $height);

        parent::__construct($label ?? $name);
    }

    /**@return Text*/
    public function getField()
    {
        return $this->field;
    }

    public function setType($type)
    {
        $this->field->setType($type);
    }
}
