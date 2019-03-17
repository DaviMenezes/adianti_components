<?php

namespace Dvi\Adianti\Widget\Util;

use Adianti\Base\Lib\Control\TAction;
use App\Http\Request;
use App\Http\RouteInfo;
use App\Http\Router;
use Exception;

/**
 * Control Action
 * @package    Control
 * @subpackage Components
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class Action extends TAction
{
    protected $is_static;
    private $route;
    protected $http_method;

    /**
     * Action constructor.
     * @noinspection PhpMissingParentConstructorInspection
     * @param string $route
     * @param string $http_method
     * @param array|null $parameters
     */
    public function __construct(string $route, string $http_method = 'GET', array $parameters = null)
    {
        $this->action = $route;
        $this->http_method = $http_method;

        if (!empty($parameters)) {
            $this->setParameters($parameters);
        }
    }

    public function serialize($format_action = true)
    {
        $collection = collect($this->param);
        $parameters = '';
        $collection->except(['offset', 'static'])->map(function ($value, $key) use (&$parameters) {
            if ($key == 'order' and empty($value)) {
                $value = 'id';
            }
            if ($key == 'direction' and empty($value)) {
                $value = 'desc';
            }
            $parameters .= '/'.$key.'/'.$value;
        });
        if (!empty($this->param['offset'])) {
            $parameters .= '/offset/'.$this->param['offset'];
        }
        if (!empty($this->param['static']) or $this->is_static) {
            $parameters .= '/&static=1';
        }
        $url = str($this->action)->ensureLeft('/')->append($parameters)->str();

        return $url;
    }

    public function isStatic($route = null)
    {
        try {
            $action = $this->prepareActionString($route);

            $dispatcher = Router::getDispatcher();
            $uri = Router::getPreparedRoute(str($action));
            $dispatch = $dispatcher->dispatch($this->http_method, $uri->str());
            Router::setRouteInfo($dispatch);
            $routeInfo = Router::getRouteInfo();

            $static = (new \ReflectionClass($routeInfo->class()))->getMethod($routeInfo->method())->isStatic();

            return $static;
        } catch (Exception $e) {
            return false;
        }
    }

    public function setStatic()
    {
        $this->is_static = true;
    }

    public function setRouteParams(string $route)
    {
        $this->route = $route;
    }

    /**
     * @param $route
     * @return string
     */
    protected function prepareActionString($route): string
    {
        $action = $route ?? $this->action;

        str($this->route)->split('/')->filter(function ($value, $key) {
            return !empty($value->str());
        })->map(function ($key, $value) use (&$action) {
            if ($value % 2 == 0) {
                return;
            }
            $action .= '/' . $key->str() . '/' . $value;
        });
        return $action;
    }
}
