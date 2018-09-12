<?php
namespace Dvi\Adianti\Widget\Base;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Datagrid\TDataGrid;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridAction;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;
use Dvi\Adianti\Route;
use Dvi\Adianti\Widget\Dialog\DMessage;
use ReflectionClass;

/**
 * Widget Base DataGrid
 *
 * @version    Dvi 1.0
 * @package    Base
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DataGrid extends TDataGrid
{
    private $grid_action_delete;
    private $grid_action_edit;

    protected $custom_actions = array();
    private $called_class;
    private $my_columns;
    private $order_default_parameters;
    private $datagrid_load_method;

    public function __construct($called_class, $function_prefix = 'grid', $use_column_id = true, $use_edit_action = false, $use_delete_action = false, $params_delete = null)
    {
        parent::__construct();

        $this->called_class = $called_class;

        $this->style ='width: 100%';
        $this->disableDefaultClick();

        if ($use_column_id) {
            $this->addCol('id', 'Id', '7%');
        }

        if ($use_edit_action) {
            $this->useEditAction($called_class);
        }

        if ($use_delete_action) {
            $this->useDeleteAction($called_class, $function_prefix, $params_delete);
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
            $class = Route::getPath($this->called_class);
            $called_class_methods = (new ReflectionClass($class))->hasMethod('loadDatagrid');
            if (!$called_class_methods) {
                DMessage::create('die', null, 'Use o método $datagrid->setDatagridLoadMethod(...) para informar qual método será usado para popular a datagrid.');
            }
            $this->setDatagridLoadMethod('loadDatagrid');
        }

        if ($this->my_columns) {
            foreach ($this->my_columns as $column) {
                /**@var DataGridColumn $column */
                $column->orderParams($this->order_default_parameters);
                $column->setDatagridLoadMethod($this->datagrid_load_method);
                $column->setOrderAction($this->called_class);

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

    public function init()
    {
        $class = get_called_class();
        $grid = new DataGrid($class);
        return $grid;
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

    public function useEditAction($class_name): TDataGridAction
    {
        $this->grid_action_edit = new TDataGridAction([$class_name, 'onEdit']);
        $this->grid_action_edit->setField('id');
        $this->grid_action_edit->setLabel('Editar');
        $this->grid_action_edit->setImage('fa:pencil blue fa-2x');

        return $this->grid_action_edit;
    }

    public function useDeleteAction($class_name, $function_prefix = 'grid', $params_delete = null): TDataGridAction
    {
        $this->grid_action_delete = new TDataGridAction([$class_name, $function_prefix . 'OnDelete'], $params_delete);
        $this->grid_action_delete->setField('id');
        $this->grid_action_delete->setLabel('Excluir');
        $this->grid_action_delete->setImage('fa:trash red fa-2x');

        return $this->grid_action_delete;
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
        $has_method = (new ReflectionClass(Route::getPath($this->called_class)))->hasMethod($method);
        if (!$has_method) {
            DMessage::create('die', null, 'O método '.$method.' informado em '."<br>".' $datagrid->setDatagridLoadMethod("'.$method.'") não existe.', false);
        }
        $this->datagrid_load_method = $method;
    }
}
