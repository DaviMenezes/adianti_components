<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Registry\TSession;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;
use Adianti\Base\Lib\Widget\Datagrid\TPageNavigation;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Dvi\Adianti\Database\DTransaction;
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

    /**@var TDataGridColumn $column_id*/
    protected $column_id;

    /**@var TAction $action_delete*/
    protected $action_delete;

    protected $panel_grid;

    use DviTPageSearch;
    use DviTPageList;
    use DviTPageForm;

    public function __construct($param)
    {
        try {
            DTransaction::open();

            parent::__construct($param);

            $this->createCurrentObject($param);

            $this->createPanelForm($param);

            $this->createActions($param);

            $this->datagrid = $this->createDataGrid($param);

            $this->createPageNavigation($param);

            $vbox = new DVBox();
            $vbox->add($this->panel);

            $vbox->add($this->datagrid);

            $vbox->add($this->pageNavigation);

            parent::add($vbox);

            $data = TSession::getValue(self::getClassName(get_called_class()) . '_form_data');
            $this->panel->setFormData((object)$data);

            DTransaction::close();
        } catch (\Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function createPanelForm($param)
    {
        parent::createPanelForm($param);

        $this->mountModelFields($param);
    }

    protected function createActions($param)
    {
        $this->createActionSave($param);
        $this->createActionSearch($param);
        $this->createActionClear($param);
    }
}
