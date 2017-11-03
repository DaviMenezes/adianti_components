<?php

namespace Dvi\Control;

use Adianti\Database\TFilter;
use Dvi\Widget\Form\DviPanelGroup;

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
            if (isset($field->search_operator)) {
                $name = $field->getName();
                $field->setValue($data->$name);
                $filters[] = new TFilter($name, $field->search_operator, $field->getSearchableValue());
            }
        }

        $param['filters'] = $filters;
        $this->onReload($param);
    }
}
