<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;
use Adianti\Base\Lib\Widget\Datagrid\TPageNavigation;
use Adianti\Base\Lib\Widget\Form\TButton;
use Dvi\Adianti\Widget\Base\DataGrid;
use Dvi\Adianti\Widget\Form\DviPanelGroup;

/**
 * Control DviStandardForm
 *
 * @version    Dvi 1.0
 * @package    Control
 * @subpackage component
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DviStandardForm extends DviControl
{
    protected $objectClass;
    /**@var DviPanelGroup $panel*/
    protected $panel;
    /**@var TAction $button_save*/
    private $button_save;
    /**@var TButton $button_clear*/
    private $button_clear;

    /**@var DataGrid $datagrid*/
//    protected $datagrid;
//    /**@var TPageNavigation $pageNavigation*/
//    protected $pageNavigation;
//    protected $grid_loaded;

    /**@var TDataGridColumn $column_id*/
//    protected $column_id;

    /**@var TAction $action_delete*/
//    protected $action_delete;

//    private $useCheckButton;
//    protected $panel_grid;
//    private $use_grid_panel;

    use DviTPageForm;

    public function __construct($param)
    {
        parent::__construct();

        $this->createPanelForm($param);

        $this->createActions();

        parent::add($this->panel);
    }

    protected function createActions()
    {
        $this->panel->addActionSave();
        $this->button_save = $this->panel->getButton();

        $this->panel->addActionClear();
        $this->button_clear = $this->panel->getButton();
    }

    protected function getButtonSave()
    {
        return $this->button_save;
    }

    protected function getButtonClear()
    {
        return $this->button_clear;
    }
}
