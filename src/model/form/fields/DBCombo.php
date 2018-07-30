<?php

namespace Dvi\Adianti\Component\Model\Form\Fields;

use Adianti\Base\Lib\Database\TRecord;
use Adianti\Base\Lib\Database\TRepository;
use Adianti\Base\Lib\Widget\Form\TField;
use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\Widget\Form\DCombo;
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
    /**@var DviModel $this->model*/
    protected $model;
    protected $value;
    protected $criteria;

    public function __construct(string $name, string $label = null, bool $required = false)
    {
        $array = explode('-', $name);
        $field_name = array_pop($array);

        $label = $label ?? $field_name;

        parent::__construct($required, $label);

        $this->field = new DCombo($name, $label, $required);

        $this->setType(new FieldTypeInt());
    }

    public function model(string $model, string $value = 'name', $criteria = null)
    {
        $this->model = $model;
        $this->value = $value;
        $this->criteria = $criteria;

        return $this;
    }

    public function getField()
    {
        $this->mountModelItems();

        return $this->field;
    }

    public function items(array $items)
    {
        $this->field->items($items);
        return $this;
    }

    public function setType(FieldTypeInterface $type)
    {
        $this->field->setType($type);
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
