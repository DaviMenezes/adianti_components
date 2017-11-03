<?php

namespace Dvi\Control;

use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Database\TCriteria;
use Adianti\Database\TRepository;
use Adianti\Registry\TSession;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TQuestion;
use Dvi\Database\DTransaction;
use Dvi\Widget\Base\DataGrid;

/**
 * Control DviTPageList
 *
 * @version    Dvi 1.0
 * @package    Control
 * @subpackage component
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait DviTPageList
{
    /**@var DataGrid $datagrid*/
    protected $datagrid;
    /**@var TPageNavigation $pageNavigation*/
    protected $pageNavigation;

    /**@var TDataGridColumn $column_id*/
    protected $column_id;
    /**@var TAction $action_edit*/
    protected $action_edit;
    /**@var TAction $action_delete*/
    protected $action_delete;

    public function onReload($param)
    {
        try {
            DTransaction::open($this->database);

            $repository = new TRepository($this->objectClass);

            if (empty($param['order'])) {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }

            $limit = 10;
            $criteria = new TCriteria();
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);

            //get the filters genereted by the child classes
            $filters = TSession::getValue(get_called_class().'_filters');

            if (!$filters and isset($param['filters']) and $param['filters']) {
                $filters = $param['filters'];
            }

            if ($filters) {
                foreach ($filters as $filter) {
                    $criteria->add($filter);
                }
            }

            $items = $repository->load($criteria, false);

            //include a checkbutton if necessary
            if ($this->use_grid_panel and $this->useCheckButton) {
                foreach ($items as $key => $item) {
                    //                    $check = new TCheckButton($key.'_check_');
//                    $check->setIndexValue($item->id);
//
//                    $item->{'check'} = $check;
//                    $form = $this->panel_grid->getForm();
//                    $form->addField($check);
                }
            }

            $this->datagrid->clear();
            if ($items) {
                $this->datagrid->addItems($items);
            }

            $criteria->resetProperties();
            $count = $repository->count($criteria);

            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);

            DTransaction::close();

            $this->grid_loaded = true;
        } catch (Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    protected function createDataGrid($createModel = true, $showId = false)
    {
        $class = get_called_class();
        $this->datagrid = new DataGrid($class);

        if ($showId) {
            $this->column_id = new TDataGridColumn('id', 'Id', 'left', '5%');
            $this->datagrid->addColumn($this->column_id);
        }

        if ($createModel) {
            $this->createDatagridModel();
        }
    }

    protected function createDatagridModel()
    {
        $this->datagrid->createModel();
    }

    protected function createPageNavigation()
    {
        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
    }

    public function show()
    {
        $args = func_get_arg(0);

        $recheck = $args['recheck'] ?? true;
        if ($recheck) {
            if (!$this->grid_loaded and (!isset($_GET['method']) or ($_GET['method'] !== 'onReload'))) {
                $this->onReload($args);
            }
        }
        parent::show();
    }

    public function gridOnDelete($param)
    {
        $action = new TAction([$this, 'delete']);

        $param['pagination_param'] = $this->getUrlPaginationParameters();

        $action->setParameters($param);

        new TQuestion(_t('Do you really want to delete ?'), $action);
    }

    public function delete($param)
    {
        try {
            DTransaction::open($this->database);

            $this->objectClass::remove($param['id']);

            DTransaction::close();

            AdiantiCoreApplication::loadPage(get_called_class(), 'onReload', $param['pagination_param']);
        } catch (Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    private function getUrlPaginationParameters(): array
    {
        $url_params = explode('&', $_SERVER['HTTP_REFERER']);
        unset($url_params[0]);//remove url ini
        unset($url_params[1]);//remove param method

        $pagination_params = array();
        foreach ($url_params as $url_param) {
            $explode = explode('=', $url_param);

            $pagination_params[$explode[0]] = $explode[1];
        }
        return $pagination_params;
    }
}
