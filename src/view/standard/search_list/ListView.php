<?php

namespace Dvi\Adianti\View\Standard\SearchList;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;
use Adianti\Base\Lib\Widget\Datagrid\TPageNavigation;
use Dvi\Adianti\Database\Transaction;
use Dvi\Adianti\View\Standard\Form\BaseFormView;
use Dvi\Adianti\View\Standard\PageFormView;
use Dvi\Adianti\Widget\Base\DataGrid;

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
abstract class ListView extends BaseFormView
{
    protected $formController;
    /**@var DataGrid $datagrid*/
    protected $datagrid;
    /**@var TPageNavigation $pageNavigation*/
    protected $pageNavigation;
    /**@var TDataGridColumn $column_id*/
    protected $column_id;
    /**@var TAction $action_delete*/
    protected $action_delete;
    protected $panel_grid;

    protected $actions_created;
    protected $view_builded;

    use PageFormView;
    use ListViewTrait;

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
            Transaction::open();

            $this->createPanel($param);

            $this->createActions();

            $this->createContentAfterPanel();

            $this->buildDatagrid();

            Transaction::close();

            $this->view_builded = true;
        } catch (\Exception $e) {
            Transaction::rollback();
            throw new \Exception('Construção da view.'.$e->getMessage());
        }
    }

    public function getContent()
    {
        $this->vbox->add($this->panel);
        $this->vbox->add($this->getContentAfterPanel());
        $this->vbox->add($this->getDatagrid());
        if ($this->datagrid) {
            $this->vbox->add($this->pageNavigation);
        }

        return $this->vbox;
    }

    public function setFormController($formController)
    {
        $this->formController = $formController;
    }
}
