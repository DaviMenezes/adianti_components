<?php

namespace Dvi\Adianti\View\Standard\SearchList;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;
use Dvi\Adianti\Control\PaginationHelper;
use Dvi\Adianti\Helpers\Utils;
use Dvi\Adianti\Widget\Base\DataGrid;
use Dvi\Adianti\Widget\Datagrid\PageNavigation;
use Dvi\Adianti\Widget\Form\PanelGroup\PanelGroup;

/**
 * View ListViewTrait
 *
 * @version    Dvi 1.0
 * @package    Control
 * @subpackage component
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait ListViewTrait
{
    /**@var PanelGroup $panel*/
    protected $panel;
    /**@var DataGrid $datagrid*/
    protected $datagrid;
    /**@var PageNavigation $pageNavigation*/
    protected $pageNavigation;
    /**@var TDataGridColumn $column_id*/
    protected $column_id;
    /**@var TAction $action_edit*/
    protected $action_edit;
    /**@var TAction $action_delete*/
    protected $action_delete;
    protected $useCheckButton;
    protected $formController;
    private $query_limit;

    public function buildDatagrid($createModel = true, $showId = false): DataGrid
    {
        $class = $this->params['class'];
        $this->datagrid = new DataGrid($class, 'grid', $showId);

        $this->datagrid->useEditAction($this->formController ?? $class);
        $this->datagrid->useDeleteAction($class);
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

    public function createPageNavigation($count, $params)
    {
        $this->pageNavigation = new PageNavigation();

        $new_params = PaginationHelper::getUrlPaginationParameters($this->params);

        if (!count($new_params)) {
            $new_params =  null;
        }

        $this->pageNavigation->setAction(new TAction([$this->params['class'], 'loadDatagrid'], $new_params));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        $this->pageNavigation->setCount($count);
        $this->pageNavigation->setProperties($params);
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

    public function createActionNew()
    {
        if (!empty($this->formController)) {
            return $this->panel
                ->footerLink([$this->formController], 'fa:plus fa-2x')->label(_t('Add'))
                ->setParameters(Utils::getNewParams());
        }
    }

    public function createActionSearch()
    {
        $this->panel->addActionSearch();
        $this->panel->getCurrentButton()
            ->getAction()
            ->setParameters(Utils::getNewParams());
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
