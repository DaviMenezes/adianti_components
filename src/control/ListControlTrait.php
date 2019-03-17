<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Registry\TSession;
use App\Http\Request;
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
    /**@var Request */
    protected $request;
    /**@var DataGrid */
    protected $datagrid;
    /**@var PageNavigation */
    protected $pageNavigation;
    protected $datagrid_items_criteria;
    protected $datagrid_items_obj_repository;
    protected $page_navigation_count;
    protected $query_limit;
    /**@var ListView */
    public $view;
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
            $this->fillDatagrid();
        } catch (\Exception $e) {
            throw new \Exception('Populando datagrid.' . $e->getMessage());
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

            $query->offset($this->request->get('offset'));

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
            $this->datagrid->addItems($items->all());
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
        $this->loadDatagrid();

        return parent::show($this->request);
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
        if ($this->request->has('order')) {
            $direction = $this->request->get('direction') ?? 'desc';

            $order = $this->request->get('order');
            TSession::setValue($session_name, ['field' => $order, 'direction' => $direction]);
            return;
        }
        $tableAlias = Reflection::shortName($this->view->getModel());
        $order = $tableAlias . '.id';
        TSession::setValue($session_name, ['field' => $order, 'direction' => $direction ?? 'desc']);
    }

    public function fillDatagrid()
    {
        $items = $this->getDatagridItems();

        $this->populateGrids($items);

        $this->preparePageNavidation();

        $this->reloaded = true;
    }
}
