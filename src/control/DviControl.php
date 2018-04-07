<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Control\TPage;
use Adianti\Base\Lib\Core\AdiantiCoreApplication;
use Adianti\Base\Lib\Core\TApplication;
use Adianti\Base\Lib\Registry\TSession;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Adianti\Base\Lib\Widget\Form\THidden;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\Model\IDviRecord;
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
 * @link https://github.com/DaviMenezes
 */
class DviControl extends TPage
{
    /**@var DviModel $currentObj*/
    protected $currentObj;

    /**@var DviPanelGroup $panel*/
    protected $panel;
    protected $database = 'default';

    use DControl;

    public function __construct($param)
    {
        parent::__construct();

        $called_class = Route::getClassName(get_called_class());

        $this->panel = new DviPanelGroup($called_class, $this->pageTitle);
        $id = new THidden('id');
        $id->setValue($param['id']?? null);
        $this->panel->addHiddenFields([$id]);

        if (empty($param['form_token'])) {
            TSession::setValue('form_token', md5(time()));
            $token = new THidden('form_token');
            $token->setValue(TSession::getValue('form_token'));
            $this->panel->addHiddenFields([$token]);
        }
    }

    public function createPanelForm($param)
    {
        if ($this->isEditing($param)) {
            $this->panel->useLabelFields(true);
        }
    }

    public static function getNewParams():array
    {
        $new_params = array();

        $url_params = explode('&', $_SERVER['QUERY_STRING']);
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
        if ((!empty($param['id']) and $param['id'] != 0) or (!empty($this->currentObj))) {
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
        if (!$this->currentObj) {
            TApplication::loadPage(get_called_class());
        }
    }

    public function show()
    {
        try {
            $args = func_get_arg(0);

            if (!$this->hasMethod($args)) {
                parent::show();
                return;
            }

            if (!$this->validateMethod($args)) {
                throw new \Exception('Método '.$args['method'].' inválido');
            }

            parent::show();
        } catch (\Exception $e) {
            new TMessage('error', 'Segurança: '.$e->getMessage());
        }
    }

    protected function hasMethod($args)
    {
        if (isset($args['method']) and $args['method']) {
            return true;
        }
        return false;
    }

    protected function validateMethod($args): bool
    {
        $rf = new \ReflectionClass(get_called_class());
        $array_methods = $rf->getMethods(\ReflectionMethod::IS_PUBLIC);
        $methods = array();
        foreach ($array_methods as $method) {
            $methods[$method->name] = $method->name;
        }
        ksort($methods);
        if (in_array($args['method'], array_keys($methods))) {
            return true;
        }
        return false;
    }

    /**
     * check if form has token and if is valid(session value)
    */
    protected function validateToken($args)
    {
        if (empty($args['form_token']) or ($args['form_token'] !== TSession::getValue('form_token'))) {
            return false;
        }
        return true;
    }
}
