<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Control\TPage;
use Adianti\Base\Lib\Core\AdiantiCoreApplication;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\Helpers\Utils;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\View\Standard\DviBaseView;
use Dvi\Adianti\Widget\Dialog\DMessage;
use Dvi\Adianti\Widget\Form\PanelGroup\DviPanelGroup;

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
abstract class DviControl extends TPage
{
    /**@var DviBaseView $view*/
    protected $view;
    protected $alread_build_view;
    /**@var DviModel $currentObj*/
    protected $currentObj;
    /**@var DviControl $pageList*/
    protected $pageList;
    protected $params;
    /**@var DviPanelGroup $panel*/
    protected $panel;
    protected $database = 'default';

    use Utils;
    use Reflection;

    public function __construct($param)
    {
        parent::__construct();
        $this->params = $param;
    }

    public function load()
    {
        $param = Utils::getNewParams();
        AdiantiCoreApplication::loadPage(get_called_class(), null, $param);
    }

    public function show()
    {
        try {
            if (!$this->validateMethod()) {
                DMessage::create('die', 'Segurança: Método '.$this->params['method'].' inválido');
            }
            parent::show();
        } catch (\Exception $e) {
            DMessage::create('die', null, $e->getMessage());
        }
    }

    abstract protected function buildView();

    protected function hasMethod($args)
    {
        if (isset($args['method']) and $args['method']) {
            return true;
        }
        return false;
    }

    protected function validateMethod(): bool
    {
        if (!isset($this->params['method'])) {
            return true;
        }
        $rf = new \ReflectionClass(get_called_class());

        $array_methods = $rf->getMethods(\ReflectionMethod::IS_PUBLIC);
        $methods = array();
        foreach ($array_methods as $method) {
            $methods[$method->name] = $method->name;
        }
        ksort($methods);
        if (in_array($this->params['method'], array_keys($methods))) {
            return true;
        }
        return false;
    }

    protected function getViewContent()
    {
        if (isset($this->view)) {
            parent::add($this->view->getContent());
        }
    }
}
