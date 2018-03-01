<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;
use Adianti\Base\Lib\Widget\Datagrid\TPageNavigation;
use Dvi\Adianti\Widget\Base\DataGrid;
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

    //    private $useCheckButton;
    protected $panel_grid;
    //    private $use_grid_panel;

    use DviTPageSearch;
    use DviTPageList;
    use DviTPageForm;

    public function __construct($param)
    {
        parent::__construct();

        $this->init($param);
    }

    public function init($param)
    {
        $this->createPanelForm($param);

        $this->datagrid = $this->createDataGrid();
        $this->createPageNavigation($param);

        $vbox = new DVBox();
        $vbox->add($this->panel);

        $vbox->add($this->datagrid);

        $vbox->add($this->pageNavigation);

        parent::add($vbox);
    }

    public function createPanelForm($param)
    {
        parent::createPanelForm($param);

        $this->createActionSearch($param);
    }
}
