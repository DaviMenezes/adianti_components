<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Core\AdiantiCoreApplication;
use Adianti\Base\Lib\Registry\TSession;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Adianti\Base\Lib\Widget\Dialog\TQuestion;
use Dvi\Adianti\Database\DTransaction;
use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\Helpers\Utils;
use Dvi\Adianti\Model\DBFormFieldPrepare;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\Model\DviTFilter;
use Dvi\Adianti\View\Standard\SearchList\StandardSearchListView;
use Dvi\Adianti\Widget\Base\DataGrid;
use Dvi\Adianti\Widget\Datagrid\DPageNavigation;

/**
 * Control ListActionsControl
 *
 * @package    Control
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait ListActionsControl
{
    /**@var DataGrid $datagrid*/
    protected $datagrid;
    /**@var DPageNavigation $pageNavigation*/
    protected $pageNavigation;
    protected $datagrid_items_criteria;
    protected $datagrid_items_obj_repository;
    protected $page_navigation_count;
    protected $query_limit;
    /**@var StandardSearchListView $view*/
    protected $view;
    protected $fields_to_sql = array();
    protected $grid_loaded =  false;
    private $reloaded;

    abstract protected function setQueryLimit();

    protected function addDatagridFilter(DviTFilter $filter)
    {
        $called_class = Reflection::getClassName(get_called_class());

        $filters = TSession::getValue($called_class . '_filters');

        $filters[$filter->field] = $filter;

        TSession::setValue($called_class . '_filters', $filters);
    }

    #region[DATAGRID ACTIONS] ***********************************************
    public static function gridOnDelete($param)
    {
        $class = $param['class'];
        $action_yes = new TAction([$class, 'delete']);
        $action_no = new TAction([$class, 'backToList']);

        $param['url_params'] = PaginationHelper::getUrlPaginationParameters($param);

        $action_yes->setParameters($param);
        $action_no->setParameters($param);

        new TQuestion(_t('Do you really want to delete ?'), $action_yes);
    }

    public function backToList()
    {
        $this->onBack();
    }

    public function delete()
    {
        try {
            DTransaction::open($this->database);

            $this->view = new $this->viewClass(array());
            $this->view->getModel()::remove($this->params['id']);

            DTransaction::close();

            $this->onBack();
        } catch (\Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    private function onBack()
    {
        unset(
            $this->params['url_params']['class'],
            $this->params['url_params']['method'],
            $this->params['url_params']['id'],
            $this->params['url_params']['key'],
            $this->params['url_params']['static']
        );
        AdiantiCoreApplication::loadPage(get_called_class(), null, $this->params['url_params'] ?? null);
    }
    #endregion

    public function loadDatagrid()
    {
        $this->getItemsAndFillDatagrid();
    }

    public function getItemsAndFillDatagrid()
    {
        try {
            if ($this->reloaded) {
                return;
            }

            $items = $this->getDatagridItems();

            DTransaction::open($this->database);
            $this->populateGrids($items);
            DTransaction::close();

            $this->preparePageNavidation();

            $this->reloaded = true;
        } catch (\Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    protected function getDatagridItems()
    {
        $this->prepareFieldsToBuildQuery();

        $query = new DBFormFieldPrepare($this->view->getModel(), get_called_class());
        $query->mountQueryByFields($this->getFieldsBuiltToQuery());

        $this->checkOrderColumn();

        $query->checkFilters(get_called_class());


        $this->setPageNavigationCount($query->count());

        $tableAlias = Reflection::getClassName($this->view->getModel());

        $order = empty($this->params['order']) ? $tableAlias .'.id' : $this->params['order'];
        $direction = empty($this->params['direction']) ? 'asc' : $this->params['direction'];

        $query->order($order, $direction);
        $query->offset($this->params['offset'] ?? null);

        return $query->get($this->query_limit);
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
        $this->view->createPageNavigation($this->page_navigation_count, $this->params);

        $this->view->addPageNavigationInBoxContainer();
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

        parent::show($this->params);
    }

    protected function prepareFieldsToBuildQuery()
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

        $filters = TSession::getValue(Reflection::getClassName(get_called_class()) . '_filters');
        if ($filters) {
            foreach ($filters as $key => $filter) {
                $pos = strpos($key, '.');
                if ($pos !== false and substr($key, 0, $pos) == Reflection::getClassName($model)) {
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
        if (isset($this->params['order_field']) and $this->params['order_field']) {
            $direction_array = ['asc' => 'desc', 'desc' => 'asc'];
            $session_name = Reflection::getClassName(get_called_class()) . '_listOrder';
            $listOrder = TSession::getValue($session_name);

            $direction = $direction_array[$listOrder['direction'] ?? 'desc'];

            $order = ['field' => $this->params['order_field'], 'direction' => $direction];
            TSession::setValue($session_name, $order);
        }
    }
}
