<?php
namespace Dvi\Adianti\Widget\Base;

use Adianti\Base\Lib\Registry\TSession;
use Adianti\Base\Lib\Widget\Base\TScript;
use Adianti\Base\Lib\Widget\Datagrid\TDataGrid;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridAction;
use App\Http\Request;
use App\Http\Router;
use ReflectionClass;

/**
 * Widget Base DataGrid
 * @package    Base
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DataGrid extends TDataGrid
{
    protected $grid_action_delete;
    protected $grid_action_edit;

    protected $custom_actions = array();
    protected $route_base;
    protected $my_columns;
    protected $order_default_parameters;
    protected $datagrid_load_method;

    public function __construct($show_id = true)
    {
        parent::__construct();

        $this->style ='width: 100%';
        $this->disableDefaultClick();

        if ($show_id) {
            $this->addCol('id', 'Id', '7%');
        }
    }

    public function createModel($create_header = true, $show_default_actions = true)
    {
        if ($show_default_actions and $this->grid_action_edit) {
            $this->addAction($this->grid_action_edit);
        }
        if ($show_default_actions and $this->grid_action_delete) {
            $this->addAction($this->grid_action_delete);
        }

        foreach ($this->custom_actions as $action) {
            $this->addAction($action);
        }

        if (!$this->datagrid_load_method) {
            $this->setDatagridLoadMethod('loadDatagrid');
        }

        if ($this->my_columns) {
            foreach ($this->my_columns as $column) {
                /**@var DataGridColumn $column */
                $column->orderParams($this->order_default_parameters);
                $column->setDatagridLoadMethod($this->datagrid_load_method);
                $column->setOrderAction();

                parent::addColumn($column);
            }
        }

        return parent::createModel($create_header);
    }

    public function setActionEdit($action)
    {
        $this->grid_action_edit = $action;
    }

    public function setActionDelete($action)
    {
        $this->grid_action_delete = $action;
    }

    public function getEditAction():TDataGridAction
    {
        return $this->grid_action_edit;
    }

    public function getDeleteAction(): TDataGridAction
    {
        return $this->grid_action_delete;
    }

    public function addActions(array $actions)
    {
        $this->custom_actions[] = $actions;
    }

    public function addCol($name, $label, $width = '100%', $align = 'left', array $order_params = null):DataGridColumn
    {
        $column = new DataGridColumn($name, $label, $align, $width);
        $column->orderParams($order_params);

        $this->my_columns[] = $column;

        return $column;
    }

    #region [ALIAS] *************************************************
    public function col($name, $label, $width = '100%', $align = 'left'):DataGridColumn
    {
        return $this->addCol($name, $label, $width, $align);
    }

    public function useEditAction($route = null): TDataGridAction
    {
        return $this->createActionEdit($route);
    }

    public function useDeleteAction($route = null, $params_delete = null): TDataGridAction
    {
        return $this->createActionDelete($route, $params_delete);
    }

    public function items(array $items, bool $clear = true)
    {
        if (count($items)) {
            if ($clear) {
                $this->clear();
            }
            $this->addItems($items);
        }
    }
    #endregion

    public function setOrderParams(array $params)
    {
        $this->order_default_parameters = $params;
    }

    public function setDatagridLoadMethod(string $method)
    {
        $class = Router::getStaticRouteInfo();
        $has_method = (new ReflectionClass($class->class()))->hasMethod($method);
        if (!$has_method) {
            throw new \Exception('O método '.$method.' informado em '."<br>".' $datagrid->setDatagridLoadMethod("'.$method.'") não existe.', false);
        }
        $this->datagrid_load_method = $method;
    }

    public function show()
    {
        $this->processTotals();

        if (!$this->hasCustomWidth()) {
            $this->{'style'} .= ';width:unset';
        }

        // shows the datagrid
        parent::show();

        // to keep browsing parameters (order, page, first_page, ...)
        collect($_REQUEST)->map(function ($value, $key) use (&$urlparams) {
            $urlparams .= $key.'/'.$value;
        });

        $route = str(TSession::delValue('route_base').'/'.$urlparams)->ensureLeft('/');

        // inline editing treatment
        TScript::create(" tdatagrid_inlineedit( '{".$route."}' );");
        TScript::create(" tdatagrid_enable_groups();");
    }

    protected function createActionEdit($route): TDataGridAction
    {
        $request = Request::instance();
        if ($request->attr('route_form_base')) {
            $route = $request->attr('route_form_base');
        } else {
            $route = $route ?? $request->attr('route_base');
        }
        $route = urlRoute($route . '/edit');
        $this->grid_action_edit = new TDataGridAction($route);
        $this->grid_action_edit->setField('id');
        $this->grid_action_edit->setLabel('Editar');
        $this->grid_action_edit->setImage('fa:pencil blue fa-2x');

        return $this->grid_action_edit;
    }

    protected function createActionDelete($route, $params_delete): TDataGridAction
    {
        $route = $route ?? urlRoute(Request::instance()->attr('route_base') . '/delete');
        $this->grid_action_delete = new TDataGridAction($route, 'GET', $params_delete);
        $this->grid_action_delete->setField('id');
        $this->grid_action_delete->setLabel('Excluir');
        $this->grid_action_delete->setImage('fa:trash red fa-2x');
        $this->grid_action_delete->setStatic();

        return $this->grid_action_delete;
    }
}
