<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Control\TPage;
use Adianti\Base\Lib\Core\AdiantiCoreApplication;
use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\Helpers\Utils;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\View\Standard\DviBaseView;
use Dvi\Adianti\Widget\Form\PanelGroup\PanelGroup;

/**
 * Trait DviControl

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
    protected $already_build_view;
    /**@var DviModel $currentObj*/
    protected $currentObj;
    /**@var DviControl $pageList*/
    protected $pageList;
    protected $request;
    /**@var PanelGroup $panel*/
    protected $panel;
    protected $database = 'default';
    protected $already_get_view_content;

    use Utils;
    use Reflection;

    public function __construct($param)
    {
        parent::__construct();
        $this->request = $param;
    }

    /**Load page without the need to pass a method*/
    public function load()
    {
        $param = Utils::getNewParams();
        AdiantiCoreApplication::loadPage(get_called_class(), null, $param);
    }

    /**Show the current page*/
    public function show()
    {
        try {
            if (!$this->validateMethod()) {
                throw new \Exception('Segurança: Método '.$this->request['method'].' inválido');
            }
            parent::show();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    abstract protected function buildView();

    /**Check if has the 'method' parameter*/
    protected function hasMethod($args)
    {
        if (isset($args['method']) and $args['method']) {
            return true;
        }
        return false;
    }

    /**Check if the past method is valid*/
    protected function validateMethod(): bool
    {
        if (!isset($this->request['method'])) {
            return true;
        }
        $rf = new \ReflectionClass(get_called_class());

        $array_methods = $rf->getMethods(\ReflectionMethod::IS_PUBLIC);
        $methods = array();
        foreach ($array_methods as $method) {
            $methods[$method->name] = $method->name;
        }
        ksort($methods);
        if (in_array($this->request['method'], array_keys($methods))) {
            return true;
        }
        return false;
    }

    protected function getViewContent()
    {
        if ($this->already_get_view_content) {
            return;
        }
        if (isset($this->view)) {
            parent::add($this->view->getContent());
            $this->already_get_view_content = true;
        }
    }
}
