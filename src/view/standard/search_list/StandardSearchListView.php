<?php

namespace Dvi\Adianti\View\Standard\SearchList;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;
use Adianti\Base\Lib\Widget\Datagrid\TPageNavigation;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Dvi\Adianti\Database\DTransaction;
use Dvi\Adianti\View\Standard\DviBaseView;
use Dvi\Adianti\View\Standard\Form\BaseFormView;
use Dvi\Adianti\View\Standard\PageFormView;
use Dvi\Adianti\Widget\Base\DataGrid;
use Dvi\Adianti\Widget\Container\DVBox;
use Dvi\Adianti\Widget\Form\PanelGroup\DviPanelGroup;

/**
 * Cria tela com formulário de pesquisa com listagem paginada
 *
 * @version    Dvi 1.0
 * @package    grid bootstrap to Adianti Framework
 * @subpackage base
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class StandardSearchListView extends BaseFormView
{
    protected $formController;
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
    /**@var DVBox $vbox*/
    protected $vbox;
    protected $actions_created;
    private $view_builded;

    use PageFormView;
    use ListView;

    public function __construct($param)
    {
        $this->setModel();
        $this->setStructureFields();

        parent::__construct($param);
    }

    public function createActions()
    {
        if ($this->actions_created) {
            return;
        }

        $this->createActionSearch();

        $this->createActionClear();

        $this->createActionNew();

        $this->actions_created = true;
    }

    public function build($param)
    {
        try {
            if ($this->view_builded) {
                return;
            }
            DTransaction::open();

            $this->createPanel($param);

            $this->createActions();

            $this->createContentAfterPanel();

            $this->buildDatagrid();

            $this->createVBoxContent();

            DTransaction::close();

            $this->view_builded = true;
        } catch (\Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function getContent()
    {
        return $this->vbox;
    }

    public function setFormController($formController)
    {
        $this->formController = $formController;
    }

    protected function createVBoxContent()
    {
        $this->vbox = new DVBox();
        $this->vbox->add($this->panel);
        $this->vbox->add($this->getContentAfterPanel());
        $this->vbox->add($this->getDatagrid());
    }

    public function addPageNavigationInBoxContainer()
    {
        $this->vbox->add($this->pageNavigation);
    }
}
