<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Control\TPage;
use Adianti\Base\Lib\Core\AdiantiCoreApplication;
use Adianti\Base\Lib\Database\TTransaction;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Dvi\Adianti\Widget\Form\DviPanelGroup;
use Exception;

/**
 * Trait DviControl
 *
 * @version    Dvi 1.0
 * @package    control
 * @subpackage trait
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DviControl extends TPage
{
    use DControl;

    /**@var DviPanelGroup $panel*/
    protected $panel;
    protected $database = 'default';

    protected $grid_loaded =  false;

    public static function getNewParams($param)
    {
        $new_params = array();

        $url_params = explode('&', $_SERVER['HTTP_REFERER']);
        unset($url_params[0]);
        foreach ($url_params as $url_param) {
            $value = explode('=', $url_param);
            $new_params[$value[0]] = $value[1];
        }
        return $new_params;
    }

    public function onClear($param)
    {
        $params = DviControl::getNewParams($param);
        unset($params['id'], $params['key']);

        AdiantiCoreApplication::loadPage(get_called_class(), null, $params);
    }

    public function load()
    {
        $param = null;
        if (func_get_args()) {
            $param =  func_get_arg(0);
        }
        
        $param = self::getNewParams($param);
        AdiantiCoreApplication::loadPage(get_called_class(), null, $param);
    }


}
