<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Control\TPage;
use Adianti\Base\Lib\Core\AdiantiCoreApplication;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Dvi\Adianti\Database\DTransaction;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\Route;
use Dvi\Adianti\Widget\Form\DviPanelGroup;

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
    /**@var DviModel $currentObj*/
    protected $currentObj;

    use DControl;

    /**@var DviPanelGroup $panel*/
    protected $panel;
    protected $database = 'default';

    protected $grid_loaded =  false;

    public function createPanelForm($param)
    {
        $called_class = Route::getClassName(get_called_class());

        $this->panel = new DviPanelGroup($called_class, $this->pageTitle);
    }

    public static function getNewParams():array
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

    public static function onClear($param)
    {
        $params = DviControl::getNewParams();
        unset($params['id'], $params['key']);

        AdiantiCoreApplication::loadPage(get_called_class(), null, $params);
    }

    public function load($param = null)
    {
        $param = self::getNewParams($param);
        AdiantiCoreApplication::loadPage(get_called_class(), null, $param);
    }

    protected function isEditing($param)
    {
        if (!empty($param['id']) and $param['id'] != 0) {
            return true;
        }
        return false;
    }

    protected function createCurrentObject($param)
    {
        $this->currentObj = $this->objectClass::find($param['id'] ?? null);
        $this->currentObj = !$this->currentObj ? new \stdClass() : $this->currentObj;
    }
}
