<?php

namespace Dvi\Adianti\Component\Model\Form\Fields;
use Adianti\Base\Lib\Database\TRecord;
use Adianti\Base\Lib\Database\TRepository;
use Adianti\Base\Lib\Database\TTransaction;
use Dvi\Adianti\Model\DBFormField;
use Dvi\Adianti\Widget\Form\DCombo;
use Dvi\Module\Finance\Model\Transaction;

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
        $array = explode('_', $name);
        $field_name = array_pop($array);

        $label = $label ?? $field_name;

        parent::__construct($name, $type, $required, $label);

        $this->field = new DCombo($name, $label, $required);
    }

    public function model(string $model, string $value = 'name', $criteria = null)
    {
        /**@var TRecord $model*/
        $repository = new TRepository($model);
        $objs = $repository->load($criteria);

        $items = array();

        foreach ($objs as $obj) {
            $relationship_obj = explode('->', $value);
            if (count($relationship_obj) > 0) {
                $prop_value = null;
                foreach ($relationship_obj as $key => $item_value) {
                    $prop_name = $relationship_obj[$key];
                    $prop_value = $prop_value ? $prop_value->$prop_name : $obj->$prop_name;
                }
            }
            $items[$obj->id] = $prop_value;
        }

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