<?php
/**
 * Column to bootstrap grid
 *
 * @version    Adianti 4.0
 * @package    grid bootstrap
 * @subpackage base
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */

namespace Dvi\Adianti\Widget\Base;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Datagrid\TDataGridColumn;

class DataGridColumn extends TDataGridColumn
{
    private $order_params;
    private $order;
    private $datagrid_load_method;

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
