<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Registry\TSession;
use App\Http\Request;
use Dvi\Adianti\Widget\Container\VBox;
use Exception;

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
    /**@var VBox */
    protected $vbox_container;
    protected $already_build_view;

    use SearchListControlTrait;
    use ListControlTrait;
    use CommonControl;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->initializeParams();
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
     * $this->request->add(['view_class' => 'MyFormListView::class']);
     * $this->request->add(['route_base' => route('/your/route/base')]);
     * $this->request->add(['route_form_base' => route('/your/route/form/base')]);
     */
    abstract public function init();

    protected function createView()
    {
        $view = $this->request->attr('view_class');
        $this->view = new $view($this->request);
    }

    /**Define query limit default to listing and pagination*/
    protected function setQueryLimit($limit = null)
    {
        $this->view->setQueryLimit($limit ?? 10);
        $this->query_limit = $limit ?? 10;
    }

    private function checkParams(Request $request)
    {
        if (!$request->routeInfo()) {
            throw new Exception('Objeto route info não informado');
        }

        if (!$this->request->attr('route_base')) {
            throw new \Exception('Informe o atributo route_base');
        }
    }

    protected function initializeParams()
    {
        $this->init();

        $this->checkParams($this->request);
    }
}
