<?php

namespace Dvi\Control;

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Adianti\Log\TLoggerSTD;
use Adianti\Registry\TSession;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TQuestion;
use Adianti\Widget\Form\TCheckButton;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\THidden;
use Dvi\Widget\Form\DviPanelGroup;
use Dvi\Widget\Base\DGridColumn;
use Exception;
use stdClass;

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

class DviTPageFormList extends DviControl
{


    protected $pageTitle;
    protected $database;
    protected $objectClass;
    /**@var DviPanelGroup $panel*/
    protected $panel;
    /**@var TDataGrid $datagrid*/
    protected $datagrid;
    /**@var TPageNavigation $pageNavigation*/
    protected $pageNavigation;
    protected $grid_loaded;

    /**@var TDataGridColumn $column_id*/
    protected $column_id;
    /**@var TAction $action_edit*/
    protected $action_edit;
    /**@var TAction $action_delete*/
    protected $action_delete;

    private $useCheckButton;
    protected $panel_grid;
    private $use_grid_panel;

    public function __construct($param, bool $useGridPanel = false)
    {
        parent::__construct();

        $this->use_grid_panel = $useGridPanel;

        $this->createPanelForm($param);
        $this->createDataGrid();
        $this->createPageNavigation();

        $vbox = new TVBox();
        $vbox->style = 'width:100%;';
        $vbox->add($this->panel);

        if ($this->use_grid_panel) {
            //Panel with datagrid
            $this->panel_grid = new DviPanelGroup(get_called_class(), 'Lista de Fardos do lote', 'gridBurdenPiece');
            $this->panel_grid->addRow([new DGridColumn($this->datagrid)]);
            $this->panel_grid->addActionSave('onSaveGridForm');

            $vbox->add($this->panel_grid);
        } else {
            $vbox->add($this->datagrid);
        }

        $vbox->add($this->pageNavigation);

        parent::add($vbox);
    }

    public static function onSaveGridForm($param)
    {

    }

    public function onReload($param)
    {
        try {
            TTransaction::open($this->database);

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

            TTransaction::close();

            $this->grid_loaded = true;
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onSave($param)
    {
        try {
            TTransaction::open($this->database);

            $this->panel->getForm()->validate();

            $data = $this->panel->getFormData();

            $obj = new $this->objectClass();
            $obj->fromArray((array)$data);
            $obj->store();

            $param['id'] = $obj->id;
            $this->setFormWithParams($param);

            TTransaction::close();

            $this->onReload($param);

            return $obj;
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onEdit($param)
    {
        try {
            if (isset($param['id'])) {
                TTransaction::open($this->database);
                $obj = new $this->objectClass($param['id']);
                $this->panel->setFormData($obj);
                TTransaction::close();
            } else {
                $this->panel->getForm()->clear();
            }
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    protected function createDatagridModel()
    {
        $this->datagrid->createModel();
    }

    protected function setFormWithParams($params)
    {
        $object = new stdClass();
        foreach ($params as $key => $value) {
            $object->$key = $value;
        }
        $this->panel->setFormData($object);
    }

    public function useCheckButton()
    {
        $this->useCheckButton = true;
    }

}
