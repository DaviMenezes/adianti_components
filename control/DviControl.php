<?php

namespace Dvi\Control;

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Database\TTransaction;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TQuestion;
use Adianti\Widget\Form\THidden;
use Dvi\Widget\Base\DataGrid;
use Exception;
use Dvi\Widget\Form\DviPanelGroup;

/**
 * Trait DviControl
 *
 * @version    Dvi 1.0
 * @package    control
 * @subpackage trait
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DviControl extends TPage
{
    use DControl;

    /**@var DviPanelGroup $panel*/
    protected $panel;
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
    protected $grid_loaded=  false;

    public function __construct()
    {
        parent::__construct();

        $name = get_called_class();
        $this->panel = new DviPanelGroup($name, $this->pageTitle);
    }

    protected function createPanelForm($param)
    {
        $id = new THidden('id');

        $this->panel->addHiddenFields([$id]);
        $this->panel->addActionSave();
        $this->panel->addActionClear();
    }

    protected function createDataGrid($createModel = true, $showId = false)
    {
        $this->datagrid = new DataGrid(self::class);

        $this->action_edit = new TDataGridAction([$this, 'onEdit']);
        $this->action_edit->setField('id');
        $this->action_edit->setLabel('Editar');
        $this->action_edit->setImage('fa:pencil-square-o blue fa-2x');
//        $this->action_delete = new TDataGridAction([$this, 'onDelete']);
//        $this->action_delete->setField('id');
//        $this->action_delete->setLabel('Apagar');
//        $this->action_delete->setImage('fa:trash-o red fa-2x');

        if ($showId) {
            $this->column_id = new TDataGridColumn('id', 'Id', 'left', '5%');
            $this->datagrid->addColumn($this->column_id);
        }

        $this->datagrid->addAction($this->action_edit);
//        $this->datagrid->addAction($this->action_delete);

        if ($createModel) {
            $this->createDatagridModel();
        }
    }

    protected function createPageNavigation()
    {
        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
    }

    public function onClear()
    {
        $this->panel->getForm()->clear();
        TApplication::loadPage(get_called_class());
    }

    public function onInitPage()
    {
        AdiantiCoreApplication::loadPage(get_called_class());
    }

    public function gridOnDelete($param)
    {
        $action = new TAction([$this, 'delete']);

        $action->setParameters($param);

        new TQuestion(_t('Do you really want to delete ?'), $action);
    }

    public function delete($param)
    {
        try {
            TTransaction::open($this->database);
            $id = $param['id'];
            $this->objectClass::remove($id);
            TTransaction::close();

            AdiantiCoreApplication::loadPage(get_called_class());

        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
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
}
