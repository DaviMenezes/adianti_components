<?php

namespace Dvi\Adianti\Widget\Util;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Core\AdiantiCoreApplication;
use Adianti\Base\Lib\Core\AdiantiCoreTranslator;
use Dvi\AdiantiExtension\Route;
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
    /**
     * Action constructor.
     * @param array $action
     * @param array|null $parameters
     * @throws Exception
     */
    public function __construct(array $action, array $parameters = null)
    {
        if (!is_object($action[0])) {
            $action[0] = Route::getPath($action[0]);
        }

        $this->action = $action;
        if (is_array($action) and isset($action[1])) {
            if (!is_callable($this->action)) {
                $action_string = $this->toString();
                $str = 'Method ^1 must receive a parameter of type ^2';
                throw new Exception(AdiantiCoreTranslator::translate($str, __METHOD__, 'Callback'). ' <br> '.
                    AdiantiCoreTranslator::translate('Check if the action (^1) exists', $action_string));
            }
        }

        if (!empty($parameters)) {
            $this->setParameters($parameters);
        }
    }

    public function serialize($format_action = true)
    {
        // check if the callback is a method of an object
        if (is_array($this->action)) {
            $class = $this->action[0];
            // get the class name
            $url['class'] = is_object($class) ? Route::getClassName(get_class($class)) : Route::getClassName($this->action[0]);
            // get the method name
            $url['method'] = $this->action[1] ?? null;
        } elseif (is_string($this->action)) {
            // otherwise the callback is a function

            // get the function name
            $url['method'] = $this->action;
        }

        // check if there are parameters
        if ($this->param) {
            $url = array_merge($url, $this->param);
        }

        if ($format_action) {
            if ($router = AdiantiCoreApplication::getRouter()) {
                return $router(http_build_query($url));
            } else {
                return 'index.php?'.http_build_query($url);
            }
        } else {
            return http_build_query($url);
        }
    }
}
