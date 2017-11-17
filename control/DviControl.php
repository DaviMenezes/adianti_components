<?php

namespace Dvi\Control;

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

    public function onClear()
    {
        $this->panel->getForm()->clear();
        AdiantiCoreApplication::loadPage(get_called_class());
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
