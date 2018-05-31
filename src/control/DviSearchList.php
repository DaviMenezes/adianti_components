<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Container\TVBox;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;
use Adianti\Base\Lib\Widget\Datagrid\TPageNavigation;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Dvi\Adianti\Database\DTransaction;
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
 * @link https://github.com/DaviMenezes
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
        try {
            DTransaction::open();

            if (empty($this->formController)) {
                $this->formController = self::getClassName($this->objectClass).'Form';
            }
            parent::__construct($param);

            $this->createCurrentObject();

            $this->createPanelForm();

            $this->mountModelFields();

            $this->createActions();

            $this->createContentAfterPanel();

            $this->createDataGrid();

            $this->createPageNavigation();

            $vbox = new TVBox();
            $vbox->style = 'width:100%;';
            $vbox->add($this->panel);

            $vbox->add($this->getContentAfterPanel());

            $vbox->add($this->getDatagrid());

            parent::add($vbox);

            DTransaction::close();
        } catch (\Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    abstract protected function mountModelFields();

    protected function createActions()
    {
        $this->createActionSearch();

        $this->createActionClear();

        $this->createActionNew();
    }
}
