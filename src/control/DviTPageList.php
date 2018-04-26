<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Core\AdiantiCoreApplication;
use Adianti\Base\Lib\Database\TCriteria;
use Adianti\Base\Lib\Database\TExpression;
use Adianti\Base\Lib\Database\TRepository;
use Adianti\Base\Lib\Registry\TSession;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;
use Adianti\Base\Lib\Widget\Datagrid\TPageNavigation;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Adianti\Base\Lib\Widget\Dialog\TQuestion;
use Dvi\Adianti\Database\DTransaction;
use Dvi\Adianti\Widget\Base\DataGrid;
use Dvi\Adianti\Widget\Datagrid\DPageNavigation;
use Dvi\Adianti\Widget\Form\DviPanelGroup;
use Exception;

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
    /**@var DviPanelGroup $panel*/
    protected $panel;

    protected static $form;

    /**@var DataGrid $datagrid*/
    protected $datagrid;
    /**@var DPageNavigation $pageNavigation*/
    protected $pageNavigation;

    /**@var TDataGridColumn $column_id*/
    protected $column_id;
    /**@var TAction $action_edit*/
    protected $action_edit;
    /**@var TAction $action_delete*/
    protected $action_delete;

    protected $grid_loaded =  false;

    private $useCheckButton;

    protected $formController;
    protected $occurrence_query_limit;

    private static function getUrlParams(): array
    {
        $url_params = explode('&', $_SERVER['QUERY_STRING']);

        return $url_params;
    }

    public function onReload()
    {
        try {
            DTransaction::open($this->database);

            $this->populateGrids();

            DTransaction::close();
        } catch (Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    protected function createDataGrid($createModel = true, $showId = false): DataGrid
    {
        $class = get_called_class();
        $this->datagrid = new DataGrid($class, 'grid', $showId);

        $this->datagrid->useEditAction($this->formController ?? get_called_class());

        $this->createDatagridColumns($showId);

        if ($createModel) {
            $this->createDatagridModel();
        }

        return $this->datagrid;
    }

    protected function getDatagrid()
    {
        return $this->datagrid;
    }

    protected function createDatagridColumns($showId = false)
    {
        $this->datagrid->col('name', 'Nome', 'left', !$showId ? '100%' : '93%');
    }

    protected function createDatagridModel($create_header = true, $show_default_actions = true)
    {
        $this->datagrid->createModel($create_header, $show_default_actions);
    }

    protected function createPageNavigation()
    {
        $this->pageNavigation = new DPageNavigation();

        $new_params = DviTPageList::getUrlPaginationParameters($this->params);

        unset($new_params['back_method']);
        $this->pageNavigation->setAction(new TAction([$this, 'onReload'], $new_params));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
    }

    public function show()
    {
        $this->params = func_get_arg(0);

        $recheck = $this->params['recheck'] ?? true;
        if ($recheck) {
            //Todo toda ação do painel não deveria chamar o reload(repopular grids), as mesmas devem ser repopuladas no início ou manualmente
            if (!$this->grid_loaded and (!isset($_GET['method']) or ($_GET['method'] !== 'onReload' and $_GET['method'] !== 'onSearch'))) {
                $this->onReload();
            }
        }
        parent::show($this->params);
    }

    public static function gridOnDelete($param)
    {
        $class = $param['class'];
        $action_yes = new TAction([$class, 'delete']);
        $action_no = new TAction([$class, 'backToList']);

        $param['url_params'] = self::getUrlPaginationParameters($param);

        $action_yes->setParameters($param);
        $action_no->setParameters($param);

        new TQuestion(_t('Do you really want to delete ?'), $action_yes);
    }

    public function backToList()
    {
        $this->params['url_params']['back_method'] = null;

        $this->onBack();
    }

    public function delete()
    {
        try {
            DTransaction::open($this->database);

            $this->objectClass::remove($this->params['id']);

            DTransaction::close();

            $this->onBack();
        } catch (Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public static function getUrlPaginationParameters($param): array
    {
        if (self::callCameFromAnotherClass($param)) {
            unset($param['class']);
            return $param;
        }

        $new_url_params = DviControl::getNewParams();

        return $new_url_params;
    }

    private static function callCameFromAnotherClass($param):bool
    {
        $url_params = self::getUrlParams();

        $class = explode('=', $url_params[0]);

        if ($class[1] !== $param['class']) {
            return true;
        }
        return false;
    }

    public static function getForm()
    {
        return self::$form;
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

    public function useCheckButton()
    {
        $this->useCheckButton = true;
    }

    protected function createActionNew()
    {
        if (!empty($this->formController)) {
            $this->panel->addCustomActionLink([$this->formController], 'fa:plus fa-2x', _t('New'), $this->params['params']?? null);
        }
    }

    protected function populateGrids()
    {
        $criteria = $this->prepareQueryCriteria();

        $repository = new TRepository($this->objectClass);

        $items = $repository->load($criteria, false);

        $this->datagrid->clear();
        if ($items) {
            $this->datagrid->addItems($items);
        }
        $this->grid_loaded = true;

        $criteria->resetProperties();
        $count = $repository->count($criteria);

        $this->pageNavigation->setCount($count);
        $this->pageNavigation->setProperties($this->params);
        $this->pageNavigation->setLimit($this->occurrence_query_limit);
    }

    protected function prepareQueryCriteria()
    {
        if (empty($this->params['order'])) {
            $this->params['order'] = 'id';
            $this->params['direction'] = 'asc';
        }

        $this->occurrence_query_limit = 10;

        $criteria = new TCriteria();
        $criteria->setProperties($this->params);
        $criteria->setProperty('limit', $this->occurrence_query_limit);

        //get the filters genereted by the child classes
        $called_class = DControl::getClassName(get_called_class());
        $filters = TSession::getValue($called_class . '_filters');

        if (!$filters and isset($this->params['filters']) and $this->params['filters']) {
            foreach ($this->params['filters'] as $filter) {
                $filters[] = $filter;
            }
        }

        if ($filters) {
            foreach ($filters as $filter) {
                $criteria->add($filter, TExpression::OR_OPERATOR);
            }
        }
        return $criteria;
    }
}
