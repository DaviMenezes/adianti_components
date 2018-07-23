<?php

namespace Dvi\Adianti\View\Standard;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;
use Adianti\Base\Lib\Widget\Datagrid\TPageNavigation;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Dvi\Adianti\Database\DTransaction;
use Dvi\Adianti\View\Standard\Form\BaseFormView;
use Dvi\Adianti\View\Standard\Form\FormView;
use Dvi\Adianti\View\Standard\SearchList\ListView;
use Dvi\Adianti\Widget\Base\DataGrid;
use Dvi\Adianti\Widget\Container\DVBox;
use Dvi\Adianti\Widget\Form\PanelGroup\DviPanelGroup;

/**
 * Control DviSearchFormList
 *
 * @version    Dvi 1.0
 * @package    Control
 * @subpackage component
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class StandardSearchFormListView extends BaseFormView
{
    /**@var DviPanelGroup $panel*/
    protected $panel;
    /**@var DataGrid $datagrid*/
    protected $datagrid;
    /**@var TPageNavigation $pageNavigation*/
    protected $pageNavigation;
    /**@var TDataGridColumn $column_id*/
    protected $column_id;
    /**@var TAction $action_delete*/
    protected $action_delete;
    protected $panel_grid;

    use ListView;
    use FormView;
    use PageFormView;

    public function __construct($param)
    {
        parent::__construct($param);

        $this->setModel();
        $this->setStructureFields();
    }

    public function build($param)
    {
        try {
            $this->createPanelForm();

            $this->createFormToken($param);

            if (!$this->alreadyCreatePanelRows()) {
                $this->buildFields();
                $this->createPanelFields();
            }
            $this->createActions();

            $this->createDataGrid();

        } catch (\Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function createPanel($param)
    {
        if ($this->panel_created) {
            return;
        }

        $this->createPanelForm();

        $this->createFormToken($param);

        $this->panel_created = true;
    }

    public function createActions()
    {
        $this->createActionSave();
        if (!$this->isEditing()) {
            $this->createActionSearch();
        }
        $this->createActionClear();
    }

    public function getContent()
    {
        $vbox = new DVBox();

        $vbox->add($this->getPanel());
        $vbox->add($this->getDatagrid());

        return $vbox;
    }
}
