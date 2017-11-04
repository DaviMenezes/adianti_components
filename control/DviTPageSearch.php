<?php

namespace Dvi\Control;

use Adianti\Database\TFilter;
use Dvi\Widget\Form\DviPanelGroup;
use Dvi\Widget\Form\Field\SearchableField;

/**
 * control DviTPageSearch
 *
 * @version    Dvi 1.0
 * @package    control
 * @subpackage component
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait DviTPageSearch
{
    protected $pageTitle;

    /**@var DviPanelGroup $panel*/
    protected $panel;

    public function createPanelForm($param)
    {
        $name = get_called_class();
        $this->panel = new DviPanelGroup($name, $this->pageTitle);
        $this->panel
            ->addActionSearch()
            ->addActionClear();
    }

    public function onSearch($param)
    {
        $fields = $this->panel->getForm()->getFields();
        $data = $this->panel->getFormData();

        $filters = array();
        foreach ($fields as $field) {
            $traits = class_uses($field);

            if (in_array(SearchableField::class, $traits)) {
                $name = $field->getName();
                if (empty($data->$name)) continue;

                $field->setValue($data->$name);
                $searchOperator = $field->getSearchOperator();
                $filters[] = new TFilter($name, $searchOperator, $field->getSearchableValue());
            }
        }

        $param['filters'] = $filters;
        $this->onReload($param);
    }
}
