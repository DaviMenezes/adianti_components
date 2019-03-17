<?php

namespace Dvi\Adianti\Widget\Base;

use Adianti\Base\Lib\Registry\TSession;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;
use App\Http\Request;
use Dvi\Adianti\Widget\Util\Action;

/**
 * Column to bootstrap grid
 *
 * @package    grid bootstrap
 * @subpackage base
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DataGridColumn extends TDataGridColumn
{
    protected $order_params;
    protected $order;
    protected $datagrid_load_method;
    protected $action;

    public function __construct($name, $label, $align = 'left', $width = '100%')
    {
        parent::__construct($name, $label, $align, $width);

        $this->order(true);
    }

    public function orderParams($params)
    {
        $request = Request::instance();
        $this->order_params = $params;
        $this->order_params['order'] = $this->getName();
        if ($request->get('direction') == 'asc') {
            $this->order_params['direction'] = 'desc';
            return $this;
        }
        $this->order_params['direction'] = 'desc';
        return $this;
    }

    public function order($order)
    {
        $this->order = $order;
        return $this;
    }

    public function setOrderAction()
    {
        if ($this->order) {
            $route = urlRoute(Request::instance()->attr('route_base'));
            $this->setAction(new Action($route, 'GET', $this->order_params));
        }
    }

    public function setDatagridLoadMethod($method)
    {
        $this->datagrid_load_method = $method;
    }
}
