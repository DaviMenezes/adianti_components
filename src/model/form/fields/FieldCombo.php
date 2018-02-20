<?php

namespace Dvi\Adianti\Component\Model\Form\Fields;
use Adianti\Base\Lib\Database\TRecord;
use Dvi\Adianti\Model\DBFormField;

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

    public function __construct(string $name, string $model, string $value, string $type, bool $required = false, string $label = null)
    {
        parent::__construct($name, $type, $required, $label);

        $this->field = new DCombo($name, $label ?? $name, $required);

        $this->model = $model;
        $this->value = $value;
    }

    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;
        return $this;
    }

    public function get()
    {
        /**@var TRecord $model*/
        $items = $this->model::getIndexedArray('id', $this->value, $this->criteria);
        $this->field->items($items);
    }
}