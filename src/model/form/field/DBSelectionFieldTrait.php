<?php

namespace Dvi\Adianti\Model\Form\Field;

use Dvi\Adianti\Model\DviModel;

/**
 * Field DBSelectionFieldTrait
 * Commons method to selection fields
 * @package    Field
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 * @see https://t.me/davimenezes
 */
trait DBSelectionFieldTrait
{
    /**@var DviModel*/
    protected $model;
    protected $value;
    protected $criteria;

    public function model(string $model, string $value = 'name', $criteria = null)
    {
        $this->model = $model;
        $this->value = $value;
        $this->criteria = $criteria;

        return $this;
    }

    public function items(array $items)
    {
        $this->field->items($items);
        return $this;
    }

    private function mountModelItems()
    {
        if (empty($this->model)) {
            return;
        }

        $objs = $this->model::query(['id', 'name'])->get();

        $items = array();

        foreach ($objs as $obj) {
            $relationship_obj = explode('->', $this->value);
            if (count($relationship_obj) > 0) {
                $prop_value = null;
                foreach ($relationship_obj as $key => $item_value) {
                    $prop_name = $relationship_obj[$key];
                    $prop_value = $obj->$prop_name;
                }
            }
            $items[$obj->id] = $prop_value;
        }

        $this->field->items($items);
    }
}
