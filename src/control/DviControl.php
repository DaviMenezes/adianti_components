<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Control\TPage;
use Adianti\Base\Lib\Core\AdiantiCoreApplication;
use Adianti\Base\Lib\Registry\TSession;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\Route;
use Dvi\Adianti\Widget\Form\DHidden;
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
    protected $params;

    use DControl;

    public function __construct($param)
    {
        $this->params = $param;

        parent::__construct();

        $called_class = Route::getClassName(get_called_class());

        $this->panel = new DviPanelGroup($called_class, $this->pageTitle);
        $field_id = new DHidden('id');
        $field_id->setValue($this->params['id'] ?? null);
        $field_token = new DHidden($called_class.'_form_token');

        $this->panel->addHiddenFields([$field_id, $field_token]);

        if (empty($this->params[$called_class.'_form_token'])) {
            $token_session = md5(microtime());
            TSession::setValue($called_class.'_form_token', $token_session);

            $field_token->setValue($token_session);
        }
    }

    protected function getPanel()
    {
        return $this->panel;
    }

    public function createPanelForm()
    {
        if ($this->isEditing()) {
            $this->panel->useLabelFields(true);
        }
    }

    public static function getNewParams():array
    {
        $new_params = array();

        $url_params = explode('&', $_SERVER['QUERY_STRING']);
        unset($url_params[0]);
        foreach ($url_params as $url_param) {
            if (!empty($url_param)) {
                $value = explode('=', $url_param);
                $new_params[$value[0]] = $value[1];
            }
        }
        
        return $new_params;
    }

    public static function onClear($param, $reload = true)
    {
        TSession::setValue(self::getClassName(get_called_class()) . '_form_data', null);
        TSession::setValue(self::getClassName(get_called_class()) . '_filters', null);

        $params = DviControl::getNewParams();
        unset($params['id'], $params['key'], $params['method'], $params['static']);

        if ($reload) {
            AdiantiCoreApplication::loadPage(self::getClassName(get_called_class()), null, $params);
        }
    }

    public function load()
    {
        $param = self::getNewParams();
        AdiantiCoreApplication::loadPage(get_called_class(), null, $param);
    }

    protected function isEditing()
    {
        if ((!empty($this->params['id']) and $this->params['id'] != 0) or (!empty($this->currentObj))) {
            return true;
        }
        return false;
    }

    protected function createCurrentObject()
    {
        if (!$this->isEditing()) {
            return;
        }
        $this->currentObj = $this->objectClass::find($this->params['id'] ?? null);
        if (!$this->currentObj) {
            throw new \Exception('Registro não encontrado');
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
    protected function validateToken()
    {
        $called_class = Route::getClassName(get_called_class());
        if (!empty($this->params[$called_class.'_form_token']) and (
            $this->params[$called_class.'_form_token'] === TSession::getValue($called_class.'_form_token'))) {
            return true;
        }
        return false;
    }
}
