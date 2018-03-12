<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Container\TVBox;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;
use Adianti\Base\Lib\Widget\Datagrid\TPageNavigation;
use Dvi\Adianti\Widget\Base\DataGrid;
use Dvi\Adianti\Widget\Form\DviPanelGroup;

/**
 * Cria tela com formulário de pesquisa com listagem paginada
 *
 * @version    Dvi 1.0
 * @package    grid bootstrap to Adianti Framework
 * @subpackage base
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */

abstract class DviSearchList extends DviControl
{
    protected $objectClass;

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
    private $use_grid_panel;

    use DviTPageForm;
    use DviTPageSearch;
    use DviTPageList;

    public function __construct($param)
    {
        parent::__construct();

        $this->createPanelForm($param);

        $this->mountModelFields($param);

        $this->createActions($param);

        $this->createDataGrid();

        $this->createPageNavigation($param);

        $vbox = new TVBox();
        $vbox->style = 'width:100%;';
        $vbox->add($this->panel);

        $vbox->add($this->datagrid);

        $vbox->add($this->pageNavigation);

        parent::add($vbox);
    }

    abstract protected function mountModelFields($param);

    protected function createActions($param)
    {
        $this->createActionSearch($param);

        $this->createActionClear($param);

        $this->createActionNew($param);
    }
}
