<?php

namespace Dvi\Adianti\Control;

use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;
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

    protected $database = 'default';

    protected $grid_loaded=  false;

    public static function getNewParams($param)
    {
        $new_params = array();

        $url_params = explode('?', $_SERVER['HTTP_REFERER']);
        $url_params = explode('&', $url_params[1]);
        foreach ($url_params as $url_param) {
            $value = explode('=', $url_param);
            if (is_array($value) and ($value[0] == 'class' or $value[0] == 'method')) {
                unset($param);
            } else {
                $new_params[$value[0]] = $value[1];
            }
        }
        return $new_params;
    }

    public function onClear($param)
    {
        $this->panel->getForm()->clear();

        $params = DviControl::getNewParams($param);
        unset($params['id']);

        AdiantiCoreApplication::loadPage(get_called_class(), null, $params);
    }

    public function onInitPage()
    {
        AdiantiCoreApplication::loadPage(get_called_class());
    }

    public function onEdit($param)
    {
        try {
            if (isset($param['id'])) {
                TTransaction::open($this->database);
                $obj = new $this->objectClass($param['id']);
                unset($param['class']);
                unset($param['method']);
                foreach ($param as $key => $value) {
                    $obj->$key = $value;
                }
                $this->panel->setFormData($obj);
                TTransaction::close();
            } else {
                unset($param['class']);
                unset($param['method']);
                $obj = new \stdClass();
                foreach ($param as $key => $value) {
                    $obj->$key = $value;
                }
                $this->panel->setFormData($obj);
            }
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }
}
