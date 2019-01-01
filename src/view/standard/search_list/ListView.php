<?php

namespace Dvi\Adianti\View\Standard\SearchList;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Registry\TSession;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;
use Adianti\Base\Lib\Widget\Datagrid\TPageNavigation;
use App\Http\Request;
use Dvi\Adianti\View\Standard\Form\BaseFormView;
use Dvi\Adianti\View\Standard\PageFormView;
use Dvi\Adianti\Widget\Base\DataGrid;

/**
 * Cria tela com formulário de pesquisa com listagem paginada
 * @package    grid bootstrap to Adianti Framework
 * @subpackage base
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class ListView extends BaseFormView
{
    /**@var DataGrid */
    protected $datagrid;
    /**@var TPageNavigation */
    protected $pageNavigation;
    /**@var TDataGridColumn */
    protected $column_id;
    /**@var TAction */
    protected $action_delete;
    protected $panel_grid;

    protected $actions_created;
    protected $view_builded;

    use PageFormView;
    use ListViewTrait;

    public function __construct(Request $request)
    {
        $this->setModel();
        $this->setStructureFields();

        parent::__construct($request);
    }

    public function createActions()
    {
        if ($this->actions_created) {
            return;
        }

        $this->createActionNew();

        $this->createActionSearch();

        $this->createActionClear();

        $this->actions_created = true;
    }

    public function build(Request $request)
    {
        try {
            if ($this->view_builded) {
                return;
            }
            $this->createPanel($request);

            $this->createActions();

            $this->createContentAfterPanel();

            $this->buildDatagrid();

            $this->view_builded = true;
        } catch (\Exception $e) {
            throw new \Exception('Construção da view.' . $e->getMessage());
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
}
