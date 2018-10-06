<?php

namespace Dvi\Adianti\Widget\Base;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;

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

    public function __construct($name, $label, $align = 'left', $width = '100%')
    {
        parent::__construct($name, $label, $align, $width);

        $this->order(true);
    }

    public function orderParams($params)
    {
        $this->order_params = $params;
        $this->order_params['order_field'] = $this->getName();

        return $this;
    }

    public function order($order)
    {
        $this->order = $order;
        return $this;
    }

    public function setOrderAction($called_class)
    {
        if ($this->order) {
            $this->setAction(new TAction([$called_class, $this->datagrid_load_method], $this->order_params));
        }
    }

    public function setDatagridLoadMethod($method)
    {
        $this->datagrid_load_method = $method;
    }
}
