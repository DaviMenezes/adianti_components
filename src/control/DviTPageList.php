<?php

namespace Dvi\Adianti\Control;

use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Database\TCriteria;
use Adianti\Database\TRepository;
use Adianti\Registry\TSession;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TQuestion;
use Dvi\Adianti\Database\DTransaction;
use Dvi\Adianti\Route;
use Dvi\Adianti\Widget\Base\DataGrid;
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
    protected static $form;

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

    private static function getUrlParams(): array
    {
        $url_params = explode('?', $_SERVER['HTTP_REFERER']);
        $url_params = explode('&', $url_params[1]);
        return $url_params;
    }

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

            if ($items) {
                $this->datagrid->clear();
                $this->datagrid->addItems($items);
            }

            $criteria->resetProperties();
            $count = $repository->count($criteria);

            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);

            DTransaction::close();

            $this->grid_loaded = true;
        } catch (Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    protected function createDataGrid($createModel = true, $showId = false): DataGrid
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

        return $this->datagrid;
    }

    protected function createDatagridModel()
    {
        $this->datagrid->createModel();
    }

    protected function createPageNavigation($param)
    {
        $this->pageNavigation = new TPageNavigation();

        $new_params = DviTPageList::getUrlPaginationParameters($param);

        $this->pageNavigation->setAction(new TAction([$this, 'onReload'], $new_params));
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

    public function backToList($param)
    {
        $param['url_params']['back_method'] = null;

        $this->onBack($param);
    }

    public function delete($param)
    {
        try {
            DTransaction::open($this->database);

            $this->objectClass::remove($param['id']);

            DTransaction::close();

            $this->onBack($param);
        } catch (Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public static function getUrlPaginationParameters($param): array
    {
        //o carregamento da classe pode ser via ajax então os parametros da url a serem considerados
        //devem ser do $param ao invés do $_SERVER
        if (self::callCameAnotherClass($param)) {
            unset($param['class']);
            return $param;
        }

        $url_params = self::getUrlParams();

        unset($url_params[0]);//remove class param

        $new_url_params = array();
        $new_url_params['back_method'] = '';

        //get param method
        foreach ($url_params as $url_param) {
            $param = explode('=', $url_param);
            if ($param[0] == 'method') {
                $new_url_params['back_method'] = $param[1];
                break;
            }
        }
        //get anothers params
        foreach ($url_params as $url_param) {
            $explode = explode('=', $url_param);

            $new_url_params[$explode[0]] = $explode[1];
        }

        if (empty($new_url_params['back_method'])) {
            unset($new_url_params['back_method']);
        }

        return $new_url_params;
    }

    private static function callCameAnotherClass($param):bool
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

    private function onBack($param)
    {
        $back_method = $param['url_params']['back_method'];
        unset($param['url_params']['method']);
        unset($param['url_params']['back_method']);

        $class = Route::getPath(get_called_class());
        AdiantiCoreApplication::loadPage($class, $back_method, $param['url_params']);
    }
}
