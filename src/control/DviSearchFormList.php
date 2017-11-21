<?php

namespace Dvi\Adianti\Control;

use Dvi\Adianti\Widget\Container\DVBox;
use Dvi\Adianti\Widget\Form\DviPanelGroup;

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
class DviSearchFormList extends DviControl
{
    protected $objectClass;
    /**@var DviPanelGroup $panel*/
    protected $panel;
    /**@var DataGrid $datagrid*/
    protected $datagrid;
    /**@var TPageNavigation $pageNavigation*/
    protected $pageNavigation;
    protected $grid_loaded;

    /**@var TDataGridColumn $column_id*/
    protected $column_id;

    /**@var TAction $action_delete*/
    protected $action_delete;

    private $useCheckButton;
    protected $panel_grid;
    private $use_grid_panel;

    use DviTPageSearch;
    use DviTPageList;

    public function __construct($param)
    {
        parent::__construct();

        $this->createPanelForm($param);
        $this->datagrid = $this->createDataGrid();
        $this->createPageNavigation();

        $vbox = new DVBox();
        $vbox->add($this->panel);

        $vbox->add($this->datagrid);

        $vbox->add($this->pageNavigation);

        parent::add($vbox);
    }
}
