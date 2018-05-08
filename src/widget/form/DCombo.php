<?php

namespace Dvi\Adianti\Widget\Form;

use Adianti\Base\Lib\Database\TRecord;
use Adianti\Base\Lib\Database\TRepository;
use Adianti\Base\Lib\Validator\TRequiredValidator;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Adianti\Base\Lib\Widget\Form\TCombo;
use Dvi\Adianti\Database\DTransaction;
use Dvi\Adianti\Widget\Form\Field\DField;
use Dvi\Adianti\Widget\Form\Field\SearchableField;
use Exception;

/**
 * Model DCombo
 *
 * @version    Dvi 1.0
 * @package    form
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DCombo extends TCombo
{
    use SearchableField;
    use DField;

    private $field_disabled;

    public function __construct(string $name, string $placeholder = null, $required = false, array $obj_array_value = null, bool $tip = true, bool $enable_search = true)
    {
        parent::__construct($name);

        $this->setLabel($placeholder);

        $this->operator('=');

        if ($placeholder) {
            $this->placeholder = $placeholder;
        }

        if ($required) {
            $this->addValidation(ucfirst($this->placeholder), new TRequiredValidator());
        }

        if ($tip) {
            //            $this->setTip(ucfirst($this->placeholder));
        }

        if ($obj_array_value) {
            $this->addItems($this->getObjItems($obj_array_value));
        }

        if ($enable_search) {
            $this->enableSearch();
        }
    }

    private function getObjItems(array $obj_array_value)
    {
        try {
            DTransaction::open();

            $result = $obj_array_value[0]::all();
            $items = array();
            if ($result) {
                foreach ($result as $item) {
                    if (!empty($obj_array_value[1])) {
                        $str_value = '';
                        foreach ($obj_array_value[1] as $key => $value) {
                            $str_value .= $item->$value. (count($obj_array_value[1]) > $key + 1 ? ' - ' : '');
                        }
                    }
                    $items[$item->id] = $str_value;
                }
            }
            DTransaction::close();

            return $items;
        } catch (Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
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

        $this->addItems($items);

        return $this;
    }

    public static function create($name, string $placeholder = null, $required = false, array $obj_array_value = null, bool $tip = true, bool $enable_search = true)
    {
        $obj = new DCombo($name, $placeholder, $required, $obj_array_value, $tip, $enable_search);
        return $obj;
    }

    public function items(array $items)
    {
        parent::addItems($items);
        return $this;
    }

    public function disable($disable = true)
    {
        $this->field_disabled = true;

        $this->setEditable(!$disable);
    }

    public function isDisabled()
    {
        return $this->field_disabled;
    }
}
