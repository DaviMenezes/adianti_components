<?php

namespace Dvi\Adianti\Helpers;

use App\TApplication;

/**
 * Helpers Redirect
 *
 * @package    Helpers
 * @subpackage Dvi
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class Redirect
{
    private $action;
    private $method;
    private $params;

    private function __construct(array $params)
    {
        $this->params = $params;
    }

    public function method(string $method)
    {
        $this->method = $method;
        return $this;
    }

    public static function loadPage(array $params = null)
    {
        $obj = new self($params);

        $obj->setAction(function ($class, $method = null, $params = null) {
            TApplication::loadPage($class, $method, $params);
        });
        return $obj;
    }

    public static function goToPage(string $params = null)
    {
        $obj = new self($params);
        $obj->setAction(function ($class, $method = null, $params = null) {
            \TApplication::goToPage($class, $method, $params);
        });
        return $obj;
    }

    public function go(string $class = null)
    {
        $action = $this->action;
        $action($class ??get_called_class(), $this->method, $this->params);
    }

    private function setAction(\Closure $param)
    {
        $this->action = $param;
    }
}
