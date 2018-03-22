<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Control\TPage;
use Adianti\Base\Lib\Core\AdiantiCoreApplication;
use Adianti\Base\Lib\Registry\TSession;
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

    /**@var DviPanelGroup $panel*/
    protected $panel;
    protected $database = 'default';

    use DControl;

    public function createPanelForm($param)
    {
        $called_class = Route::getClassName(get_called_class());

        $this->panel = new DviPanelGroup($called_class, $this->pageTitle);

        if ($this->isEditing($param)) {
            $this->panel->useLabelFields(true);
        }
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
        TSession::setValue(self::getClassName(get_called_class()) . '_form_data', null);
        TSession::setValue(self::getClassName(get_called_class()) . '_filters', null);

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
        if (!empty($param['method']) and $param['method'] == 'onEdit' and !empty($param['id']) and $param['id'] != 0) {
            return true;
        }
        return false;
    }

    protected function createCurrentObject($param)
    {
        if (!$this->isEditing($param)) {
            return;
        }
        $this->currentObj = $this->objectClass::find($param['id'] ?? null);
        $this->currentObj = !$this->currentObj ? new \stdClass() : $this->currentObj;
    }
}
