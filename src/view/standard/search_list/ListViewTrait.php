<?php

namespace Dvi\Adianti\View\Standard\SearchList;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;
use App\Http\Request;
use Dvi\Adianti\Widget\Base\DataGrid;
use Dvi\Adianti\Widget\Datagrid\PageNavigation;
use Dvi\Adianti\Widget\Form\PanelGroup\PanelGroup;
use Dvi\Adianti\Widget\Util\Action;

/**
 * View ListViewTrait
 * @package    Control
 * @subpackage component
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait ListViewTrait
{
    /**@var Request*/
    protected $request;
    /**@var PanelGroup*/
    protected $panel;
    /**@var DataGrid*/
    protected $datagrid;
    /**@var PageNavigation*/
    protected $pageNavigation;
    /**@var TDataGridColumn*/
    protected $column_id;
    /**@var TAction*/
    protected $action_edit;
    /**@var TAction*/
    protected $action_delete;
    protected $useCheckButton;
    protected $query_limit;

    public function buildDatagrid($createModel = true, $showId = false): DataGrid
    {
        $this->datagrid = new DataGrid($showId);
        $this->datagrid->useEditAction();
        $this->datagrid->useDeleteAction();
        $this->createDatagridColumns($showId);

        if ($createModel) {
            $this->createDatagridModel();
        }

        return $this->datagrid;
    }

    public function getDatagrid()
    {
        return $this->datagrid;
    }

    public function getPageNavigation()
    {
        return $this->pageNavigation;
    }

    public function createDatagridColumns($showId = false)
    {
        $this->datagrid->col('name', 'Nome', !$showId ? '100%' : '93%');
    }

    public function createDatagridModel($create_header = true, $show_default_actions = true)
    {
        $this->datagrid->createModel($create_header, $show_default_actions);
    }

    public function createPageNavigation($count, Request $request)
    {
        $this->pageNavigation = new PageNavigation();

        $new_params = $this->request->obj()->query->all();

        if (!count($new_params)) {
            $new_params =  null;
        }

        $this->pageNavigation->setAction(new Action(urlRoute($this->request->attr('route_base').'/load'), 'GET', $new_params));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        $this->pageNavigation->setCount($count);
        $this->pageNavigation->setProperties($request->getParameters());
        $this->pageNavigation->setLimit($this->query_limit);
    }

    public function addPageNavigationInBoxContainer()
    {
        if ($this->alreadyAddPagenavigation()) {
            return;
        }
        if ($this->pageNavigation) {
            $this->vbox->add($this->pageNavigation);
        }
    }

    protected function alreadyAddPagenavigation()
    {
        foreach ($this->vbox->getChilds() as $item) {
            if (is_a($item, PageNavigation::class)) {
                return true;
            }
        }
        return false;
    }

    public function useCheckButton()
    {
        $this->useCheckButton = true;
    }

    public function createActionNew($route = null)
    {
        $route = $route ?? urlRoute($this->request->attr('route_form_base').'/create');
        if ($route) {
            return $this->panel
                ->footerLink($route, 'fa:plus fa-2x')->label(_t('Add'));
//Todo check                ->setParameters(Utils::getNewParams($this->request));
        }
    }

    public function createActionSearch($route = null)
    {
        $this->panel->addActionSearch($route);
    }

    public function setQueryLimit($limit)
    {
        $this->query_limit = $limit;
    }

    public function getQueryLimit()
    {
        return $this->query_limit;
    }
}
