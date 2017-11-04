<?php

namespace Dvi\Widget\Form;

use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TCombo;
use Dvi\Database\DTransaction;
use Dvi\Widget\Form\Field\SearchableField;


/**
 * Model DCombo
 *
 * @version    Dvi 1.0
 * @package    form
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DCombo extends TCombo
{
    use SearchableField;

    public function __construct($name, string $placeholder = null, $required = false, array $obj_array_value = null, bool $tip = true, bool $enable_search = true)
    {
        parent::__construct($name);

        $this->operator('=');

        if ($placeholder) {
            $this->placeholder = $placeholder;
        }

        if ($required) {
            $this->addValidation(ucfirst($this->placeholder), new TRequiredValidator());
        }

        if ($tip) {
            $this->setTip(ucfirst($this->placeholder));
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
                    if ($obj_array_value[1]) {
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

    public static function create($name, string $placeholder = null, $required = false, array $obj_array_value = null, bool $tip = true, bool $enable_search = true)
    {
        $obj = new DCombo($name, $placeholder, $required, $obj_array_value, $tip, $enable_search);
        return $obj;
    }
}