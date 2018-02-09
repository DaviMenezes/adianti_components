<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Core\AdiantiCoreTranslator;
use Dvi\Adianti\Route;
use Exception;

/**
 * Control DAction
 *
 * @version    Dvi 1.0
 * @package    Control
 * @subpackage Components
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DAction extends TAction
{
    /**
     * DAction constructor.
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
}