<?php
namespace Dvi\Widget\Base;

use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Dvi\Widget\Base\DataGridColumn;

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
    public $grid_action_delete;

    public function __construct($class_name, $function_prefix = 'grid', $use_column_id = true, $params_delete = null)
    {
        parent::__construct();

        $this->style ='width: 100%';
        $this->disableDefaultClick();

        if ($use_column_id) {
            $this->addCol('id', 'Id', 'left', '7%');
        }

        $this->grid_action_delete = new TDataGridAction([$class_name, $function_prefix . 'OnDelete'], $params_delete);
        $this->grid_action_delete->setField('id');
        $this->grid_action_delete->setImage('fa:trash red fa-2x');
        $this->addAction($this->grid_action_delete);

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

    static public function newInstance($prefix)
    {
        $name = get_called_class();
        $obj = new DataGrid($name, $prefix);
        return $obj;
    }
}