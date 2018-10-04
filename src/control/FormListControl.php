<?php

namespace Dvi\Adianti\Control;

use Dvi\Adianti\Database\Transaction;
use Dvi\Adianti\View\Standard\FormListView;

/**
 * Control FormListControl
 *
 * @package    Control
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class FormListControl extends DviControl
{
    protected $viewClass;
    protected $formController;

    use FormControlTrait;
    use SearchListControlTrait;
    use ListControlTrait;
    use CommonActions;
    use PaginationHelper;

    public function __construct($param)
    {
        try {
            $this->formController = get_called_class();

            if ($this->already_build_view) {
                return;
            }
            parent::__construct($param);

            Transaction::open();

            $this->init();

            $this->validateViewClass();

            $this->setQueryLimit();

            $this->createView($param);

            $this->createCurrentObject();

            Transaction::close();
        } catch (\Exception $e) {
            Transaction::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /** @example $this->viewClass = MyFormListView::class; */
    abstract public function init();

    protected function setViewClass(FormListView $view_class)
    {
        $this->viewClass = $view_class;
    }

    protected function createPanel()
    {
        $this->view->createPanel($this->params);
    }

    protected function buildView()
    {
        if ($this->already_build_view) {
            return;
        }
        $this->view->build($this->params);

        $this->datagrid = $this->view->getDatagrid();
        $this->pageNavigation = $this->view->getPageNavigation();

        $this->already_build_view = true;
    }

    public function validateViewClass()
    {
        if (!is_subclass_of($this->viewClass, FormListView::class)) {
            $str = 'Uma classe do tipo ' . (new \ReflectionClass(self::class))->getShortName();
            $str .= ' deve ter uma view do tipo ' . (new \ReflectionClass(FormListView::class))->getShortName();
            throw new \Exception($str);
        }
    }

    protected function createView($param)
    {
        $this->view = new $this->viewClass($param);
    }

    protected function setQueryLimit()
    {
        $this->query_limit = 10;
    }

    public function onEdit()
    {
        $this->edit();

        $this->loadDatagrid();

        $this->view->addPageNavigationInBoxContainer();
    }

    public function show()
    {
        if (!isset($_GET['method'])) {
            $this->loadDatagrid();
        }

        parent::show();
    }
}
