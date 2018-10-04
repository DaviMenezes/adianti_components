<?php

namespace Dvi\Adianti\View\Standard;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;
use Adianti\Base\Lib\Widget\Datagrid\TPageNavigation;
use Dvi\Adianti\Database\Transaction;
use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\View\Standard\Form\BaseFormView;
use Dvi\Adianti\View\Standard\Form\FormViewTrait;
use Dvi\Adianti\View\Standard\SearchList\ListViewTrait;
use Dvi\Adianti\Widget\Base\DataGrid;
use Dvi\Adianti\Widget\Form\PanelGroup\PanelGroup;

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
abstract class FormListView extends BaseFormView
{
    /**@var DataGrid $datagrid */
    protected $datagrid;
    /**@var TPageNavigation $pageNavigation */
    protected $pageNavigation;
    /**@var TDataGridColumn $column_id */
    protected $column_id;
    /**@var TAction $action_delete */
    protected $action_delete;
    protected $panel_grid;

    use ListViewTrait;
    use FormViewTrait;
    use PageFormView;

    public function __construct($param)
    {
        parent::__construct($param);

        try {
            $this->setModel();
            $this->validateModel();
            $this->setStructureFields();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function build($param)
    {
        try {
            $this->buildForm($param);

            $this->buildDatagrid();
        } catch (\Exception $e) {
            Transaction::rollback();
            throw $e;
        }
    }

    public function createPanel($param)
    {
        if ($this->panel_created) {
            return;
        }

        $this->createPanelForm();

        $this->createFormToken($param);

        $this->panel_created = true;
    }

    public function createActions()
    {
        $this->createActionSave();
        if (!$this->isEditing()) {
            $this->createActionSearch();
        }
        $this->createActionClear();
    }

    public function getContent()
    {
        $this->vbox->add($this->getPanel());
        $this->vbox->add($this->getContentAfterPanel());
        $this->vbox->add($this->getDatagrid());
        if ($this->pageNavigation) {
            $this->vbox->add($this->getPageNavigation());
        }

        return $this->vbox;
    }

    protected function validateModel()
    {
        if (!is_subclass_of($this->model, DviModel::class)) {
            throw new \Exception('O modelo em ' . Reflection::shortName(get_called_class()) . ' deve ser filho de ' . DviModel::class);
        }
    }
}
