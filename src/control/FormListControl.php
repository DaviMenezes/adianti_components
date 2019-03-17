<?php

namespace Dvi\Adianti\Control;

use App\Http\Request;
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
    use FormControlTrait;
    use SearchListControlTrait;
    use ListControlTrait;
    use CommonControl;

    public function __construct(Request $request)
    {
        try {
            if ($this->already_build_view) {
                return;
            }
            parent::__construct($request);

            Transaction::open();

            $this->init();

            $this->validateViewClass();

            $this->setQueryLimit();

            $this->createView($request);

            $this->createCurrentObject();

            Transaction::close();
        } catch (\Exception $e) {
            Transaction::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /** @example $this->request->add(['view_class' => MyFormListView::class]); */
    abstract public function init();

    protected function createPanel()
    {
        $this->view->createPanel($this->request);
    }

    protected function buildView()
    {
        if ($this->already_build_view) {
            return;
        }
        $this->view->build($this->request);

        $this->datagrid = $this->view->getDatagrid();
        $this->pageNavigation = $this->view->getPageNavigation();

        $this->already_build_view = true;
    }

    public function validateViewClass()
    {
        if (!is_subclass_of($this->request->attr('view_class'), FormListView::class)) {
            $str = 'Uma classe do tipo ' . (new \ReflectionClass(self::class))->getShortName();
            $str .= ' deve ter uma view do tipo ' . (new \ReflectionClass(FormListView::class))->getShortName();
            throw new \Exception($str);
        }
    }

    protected function createView(Request $request)
    {
        $view = $this->request->attr('view_class');
        $this->view = new $view($request);
    }

    protected function setQueryLimit()
    {
        $this->query_limit = 10;
    }

    public function onEdit(Request $request)
    {
        $this->edit($request);

        $this->loadDatagrid();

        $this->view->addPageNavigationInBoxContainer();

        $request->add(['editing' => true]);
    }
}
