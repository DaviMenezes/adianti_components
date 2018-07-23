<?php

namespace Dvi\Adianti\Control;

use Dvi\Adianti\Helpers\CommonActions;
use Dvi\Adianti\Widget\Container\DVBox;

/**
 * Control StandardSearchListControl
 *
 * @package    Control
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class StandardSearchListControl extends DviControl implements StandardSearchListInterface
{
    protected $viewClass;
    protected $formController;
    /**@var DVBox $vbox_container*/
    protected $vbox_container;
    private $already_build_view;

//    use FormControl;
    use SearchActionsControl;
    use ListActionsControl;
    use CommonActions;

    public function __construct($param)
    {
        parent::__construct($param);

        $this->init();

        $this->setQueryLimit();
    }

    protected function buildView($param)
    {
        if ($this->already_build_view) {
            return;
        }
        $this->createView($param);

        $this->view->build($param);

        $this->vbox_container = $this->view->getContent();

        parent::add($this->vbox_container);

        $this->datagrid = $this->view->getDatagrid();
        $this->pageNavigation = $this->view->getPageNavigation();

        $this->already_build_view = true;
    }

    /**@example
     * $this->viewClass = MyFormListView::class;
     * $this->formController = MyControllerForm::class;
     */
    abstract public function init();

    protected function createView($param)
    {
        $this->view = new $this->viewClass($param);
        $this->view->setFormController($this->formController);
    }

    protected function setQueryLimit()
    {
        $this->query_limit = 10;
    }
}
