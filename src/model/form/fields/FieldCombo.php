<?php

namespace Dvi\Adianti\Component\Model\Form\Fields;
use Adianti\Base\Lib\Database\TRecord;
use Dvi\Adianti\Model\DBFormField;
use Dvi\Adianti\Widget\Form\DCombo;

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
class FieldCombo extends DBFormField
{
    private $value;
    private $criteria;
    private $model;

    public function __construct(string $name, string $type, bool $required = false, string $label = null)
    {
        parent::__construct($name, $type, $required, $label);

        $this->field = new DCombo($name, $label ?? $name, $required);
    }

    public function model(string $model, string $value, $criteria = null)
    {
        /**@var TRecord $model*/
        $items = $model::getIndexedArray('id', $value, $criteria);
        $this->field->items($items);

        return $this;
    }

    public function getField(): DCombo
    {
        return $this->field;
    }

    public function items(array $items)
    {
        $this->field->items($items);
    }
}