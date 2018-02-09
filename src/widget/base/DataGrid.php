<?php
namespace Dvi\Adianti\Widget\Base;

use Adianti\Base\Lib\Widget\Datagrid\TDataGrid;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridAction;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;
use Dvi\Adianti\Control\DviSearchFormList;
use Dvi\Adianti\Control\DviTPageList;
use Dvi\Adianti\Route;
use Dvi\Adianti\Widget\Base\DataGridColumn;

/**
 * Manipulação de grids bootstraps
 *
 * @version    Dvi 1.0
 * @package    grid bootstrap to Adianti Framework
 * @subpackage base
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DataGrid extends TDataGrid
{
    private $grid_action_delete;
    private $grid_action_edit;

    protected $custom_actions = array();

    public function __construct($class_name, $function_prefix = 'grid', $use_column_id = true, $params_delete = null)
    {
        parent::__construct();


        $this->style ='width: 100%';
        $this->disableDefaultClick();

        if ($use_column_id) {
            $this->addCol('id', 'Id', 'left', '7%');
        }

        if (!is_subclass_of($class_name, DviSearchFormList::class)) {
            $this->grid_action_edit = new TDataGridAction([$class_name, 'onEdit']);
            $this->grid_action_edit->setField('id');
            $this->grid_action_edit->setLabel('Editar');
            $this->grid_action_edit->setImage('fa:pencil-square-o blue fa-2x');
        }

        $this->grid_action_delete = new TDataGridAction([$class_name, $function_prefix . 'OnDelete'], $params_delete);
        $this->grid_action_delete->setField('id');
        $this->grid_action_delete->setImage('fa:trash red fa-2x');
    }

    public function createModel($create_header = true)
    {
        $this->addAction($this->grid_action_edit);
        $this->addAction($this->grid_action_delete);

        foreach ($this->custom_actions as $action) {
            $this->addAction($action);
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

    public function getActionEdit()
    {
        return $this->grid_action_edit;
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

    public function addCol($name, $label, $align, $width)
    {
        parent::addColumn(new TDataGridColumn($name, $label, $align, $width));
    }

    #region [ALIAS] *************************************************
    public function col($name, $label, $align, $width)
    {
        $this->addCol($name, $label, $align, $width);
    }
    #endregion
}
