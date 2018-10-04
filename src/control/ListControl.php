<?php

namespace Dvi\Adianti\Control;

use Dvi\Adianti\Widget\Container\VBox;

/**
 * Control ListControl
 *
 * @package    Control
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class ListControl extends DviControl implements StandardSearchListInterface
{
    protected $viewClass;
    protected $formController;
    /**@var VBox $vbox_container */
    protected $vbox_container;
    protected $already_build_view;

    use SearchListControlTrait;
    use ListControlTrait;
    use CommonActions;

    public function __construct($param)
    {
        parent::__construct($param);

        $this->init();
    }

    protected function buildView()
    {
        if ($this->already_build_view) {
            return;
        }
        $this->createView();

        $this->setQueryLimit();

        $this->view->build($this->request);

        $this->datagrid = $this->view->getDatagrid();
        $this->pageNavigation = $this->view->getPageNavigation();

        $this->already_build_view = true;
    }

    /**@example
     * $this->viewClass = MyFormListView::class;
     * $this->formController = MyControllerForm::class;
     */
    abstract public function init();

    protected function createView()
    {
        $this->view = new $this->viewClass($this->request);
        $this->view->setFormController($this->formController);
    }

    protected function setQueryLimit($limit = null)
    {
        $this->view->setQueryLimit($limit ?? 10);
        $this->query_limit = $limit ?? 10;
    }

    public function loadDatagrid()
    {
        $this->buildView();
        $this->getItemsAndFillDatagrid();
        if (isset($_GET['method'])) {
            $this->getViewContent();
        }
    }

    public function show()
    {
        if (!isset($_GET['method'])) {
            $this->buildView();
            $this->loadDatagrid();
            $this->getViewContent();
        }

        parent::show();
    }
}
