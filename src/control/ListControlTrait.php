<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Registry\TSession;
use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\Model\DBFormFieldPrepare;
use Dvi\Adianti\Model\QueryFilter;
use Dvi\Adianti\View\Standard\SearchList\ListView;
use Dvi\Adianti\Widget\Base\DataGrid;
use Dvi\Adianti\Widget\Datagrid\PageNavigation;

/**
 * Control ListControlTrait
 *
 * @package    Control
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait ListControlTrait
{
    /**@var DataGrid $datagrid */
    protected $datagrid;
    /**@var PageNavigation $pageNavigation */
    protected $pageNavigation;
    protected $datagrid_items_criteria;
    protected $datagrid_items_obj_repository;
    protected $page_navigation_count;
    protected $query_limit;
    /**@var ListView $view */
    protected $view;
    protected $fields_to_sql = array();
    protected $grid_loaded = false;
    protected $reloaded;

    abstract protected function setQueryLimit();

    protected function addDatagridFilter(QueryFilter $filter)
    {
        $called_class = Reflection::shortName(get_called_class());

        $filters = TSession::getValue($called_class . '_filters');

        $filters[$filter->field] = $filter;

        TSession::setValue($called_class . '_filters', $filters);
    }

    public function loadDatagrid()
    {
        $this->buildView();
        $this->getItemsAndFillDatagrid();
        $this->getViewContent();
    }

    public function getItemsAndFillDatagrid()
    {
        try {
            if ($this->reloaded) {
                return;
            }

            $items = $this->getDatagridItems();

            $this->populateGrids($items);

            $this->preparePageNavidation();

            $this->reloaded = true;
        } catch (\Exception $e) {
            throw new \Exception('Obtendo items para datagrid.' . $e->getMessage());
        }
    }

    protected function getDatagridItems()
    {
        try {
            $this->prepareSqlFieldsToBuildQuery();

            $query = new DBFormFieldPrepare($this->view->getModel(), get_called_class());
            $query->mountQueryByFields($this->getFieldsBuiltToQuery());

            $this->checkOrderColumn();

            $query->checkFilters(get_called_class());

            $this->setPageNavigationCount($query->count());
            $query->offset($this->request['offset'] ?? null);

            return $query->get($this->query_limit);
        } catch (\Exception $e) {
            $session_name = Reflection::shortName(get_called_class()) . '_listOrder';
            TSession::setValue($session_name, null);
            throw new \Exception('Montando query para popular datagrid ' . $e->getMessage());
        }
    }

    protected function createCustomSqlFields()
    {
        return [];
    }

    protected function setPageNavigationCount($count)
    {
        $this->page_navigation_count = $count;
    }

    protected function populateGrids($items)
    {
        $this->datagrid->clear();
        if ($items) {
            $this->datagrid->addItems($items);
        }
        $this->grid_loaded = true;
    }

    protected function preparePageNavidation()
    {
        if ($this->page_navigation_count <= $this->query_limit) {
            return;
        }
        $this->view->createPageNavigation($this->page_navigation_count, $this->request);
    }

    public function show()
    {
        $method = TSession::getValue('method') ?? $_GET['method'] ?? null;
        $black_list_methods = ['loadDatagrid', 'onSearch'];
        TSession::setValue('method', null);

        if (!$this->grid_loaded and (!isset($method) or (!in_array($method, $black_list_methods)))) {
            if ($method !== 'onSave') {
                $this->loadDatagrid();
            }
        }

        parent::show($this->request);
    }

    protected function prepareSqlFieldsToBuildQuery()
    {
        $this->fields_to_sql = array();
        $this->fields_to_sql['id'] = 'id';

        $model = $this->view->getModel();
        if ($this->datagrid) {
            foreach ($this->datagrid->getColumns() as $column) {
                $column_name = $column->getName();
                if (strpos($column_name, '{') !== false) {
                    continue;
                }
                if (strpos($column_name, '.') === false) {
                    if (!(new \ReflectionClass($model))->hasProperty($column_name)) {
                        continue;
                    }
                }
                $this->fields_to_sql[$column_name] = $column_name;
            }
        }
        foreach ($this->createCustomSqlFields() as $field) {
            $this->fields_to_sql[$field] = $field;
        }

        $filters = TSession::getValue(Reflection::shortName(get_called_class()) . '_filters');
        if ($filters) {
            foreach ($filters as $key => $filter) {
                $pos = strpos($key, '.');
                if ($pos !== false and substr($key, 0, $pos) == Reflection::shortName($model)) {
                    continue;
                }
                $this->fields_to_sql[strtolower($key)] = strtolower($key);
            }
        }
    }

    protected function getFieldsBuiltToQuery()
    {
        return $this->fields_to_sql;
    }

    protected function checkOrderColumn()
    {
        $session_name = Reflection::shortName(get_called_class()) . '_listOrder';
        if (isset($this->request['order_field']) and $this->request['order_field']) {
            $direction_array = ['asc' => 'desc', 'desc' => 'asc'];
            $listOrder = TSession::getValue($session_name);

            $direction = $direction_array[$listOrder['direction'] ?? 'desc'];

            $order = $this->request['order_field'];
            TSession::setValue($session_name, ['field' => $order, 'direction' => $direction ?? 'asc']);
            return;
        }
        $tableAlias = Reflection::shortName($this->view->getModel());
        $order = $tableAlias . '.id';
        TSession::setValue($session_name, ['field' => $order, 'direction' => $direction ?? 'asc']);
    }
}
