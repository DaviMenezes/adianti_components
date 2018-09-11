<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Dvi\Adianti\Database\DTransaction;
use Dvi\Adianti\Helpers\CommonActions;

/**
 * Control StandardFormListControl
 *
 * @package    Control
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class StandardFormListControl extends DviControl
{
    protected $viewClass;
    protected $formController;

    use FormControl;
    use SearchActionsControl;
    use ListActionsControl;
    use CommonActions;
    use PaginationHelper;

    public function __construct($param)
    {
        try {
            if ($this->alread_build_view) {
                return;
            }
            parent::__construct($param);

            DTransaction::open();

            $this->init();

            $this->setQueryLimit();

            $this->createView($param);

            $this->createCurrentObject();

            DTransaction::close();
        } catch (\Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    protected function createPanel()
    {
        $this->view->createPanel($this->params);
    }

    protected function buildView()
    {
        if ($this->alread_build_view) {
            return;
        }
        $this->view->build($this->params);

        $this->datagrid = $this->view->getDatagrid();
        $this->pageNavigation = $this->view->getPageNavigation();

        parent::add($this->view->getContent());

        $this->alread_build_view = true;
    }
    /**@example
    $this->viewClass = MyFormListView::class;
    $this->pageTitle = 'My page';
    $this->formController = MyControllerForm::class;*/
    abstract public function init();

    protected function createView($param)
    {
        $this->view = new $this->viewClass($param);
    }

    protected function setQueryLimit()
    {
        $this->query_limit = 10;
    }
}
