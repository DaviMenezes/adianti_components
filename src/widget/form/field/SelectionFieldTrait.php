<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Database\TRecord;
use Adianti\Base\Lib\Database\TRepository;
use Dvi\Adianti\Database\Transaction;
use Exception;

/**
 * Field SelecttionFieldTrait
 *
 * @package    Field
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 * @see https://t.me/davimenezes
 */
trait SelectionFieldTrait
{
    protected function getObjItems(array $obj_array_value)
    {
        try {
            Transaction::open();
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
            Transaction::close();

            return $items;
        } catch (Exception $e) {
            Transaction::rollback();
            throw $e;
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

    public function items(array $items)
    {
        $this->addItems($items);
        return $this;
    }
}
