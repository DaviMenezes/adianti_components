<?php

namespace Dvi\Adianti\View\Standard\SearchList;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;
use Dvi\Adianti\Control\PaginationHelper;
use Dvi\Adianti\Helpers\Utils;
use Dvi\Adianti\Widget\Base\DataGrid;
use Dvi\Adianti\Widget\Datagrid\DPageNavigation;
use Dvi\Adianti\Widget\Form\PanelGroup\DviPanelGroup;

/**
 * View ListView
 *
 * @version    Dvi 1.0
 * @package    Control
 * @subpackage component
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait ListView
{
    /**@var DviPanelGroup $panel*/
    protected $panel;
    /**@var DataGrid $datagrid*/
    protected $datagrid;
    /**@var DPageNavigation $pageNavigation*/
    protected $pageNavigation;
    /**@var TDataGridColumn $column_id*/
    protected $column_id;
    /**@var TAction $action_edit*/
    protected $action_edit;
    /**@var TAction $action_delete*/
    protected $action_delete;
    protected $useCheckButton;
    protected $formController;

    public function createDataGrid($createModel = true, $showId = false): DataGrid
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
        $this->pageNavigation = new DPageNavigation();

        $new_params = PaginationHelper::getUrlPaginationParameters($this->params);

        if (!count($new_params)) {
            $new_params =  null;
        }

        $this->pageNavigation->setAction(new TAction([$this->params['class'], 'onReload'], $new_params));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        $this->pageNavigation->setCount($count);
        $this->pageNavigation->setProperties($params);
        $this->pageNavigation->setLimit(10);
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
}
