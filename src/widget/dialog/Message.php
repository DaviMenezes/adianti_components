<?php

namespace Dvi\Adianti\Widget\Dialog;

use Adianti\Base\Lib\Core\AdiantiCoreTranslator;
use Adianti\Base\Lib\Widget\Base\TScript;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Dvi\Adianti\Helpers\Utils;
use Dvi\Adianti\Widget\Util\Action;

/**
 * Dialog Message
 *
 * @package    Dialog
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class Message
{
    private function __construct()
    {
    }

    public static function create(string $type, string $user_message = null, string $dev_message = null, $scpape_str = true, Action $action = null, string $title_msg = '')
    {
        $config = parse_ini_file("app/config/application.ini");
        $environment =  $config['environment'];

        $title    = $title_msg ? $title_msg : ($type == 'info' ? AdiantiCoreTranslator::translate('Information') : AdiantiCoreTranslator::translate('Error'));
        $callback = "function () {}";

        if ($action) {
            $callback = "function () { __adianti_load_page('{$action->serialize()}') }";
        }

        $title = $scpape_str ? addslashes($title) : $title;
        if (!$user_message) {
            $user_message = 'Solicitação inválida. Informe o administrador.';
        }
        $message = $scpape_str ? addslashes($user_message) : $user_message;

        if ($environment == 'development') {
            $bt = debug_backtrace();
            $caller = (object)array_shift($bt);
            $class = self::getFileInfo($caller->file);
            $message .= "<hr>".'A error occurred in '.$class.' in line: '.$caller->line;
            $message .= "<br>".$dev_message;
        }

        if ($type == 'die') {
            TScript::create("__adianti_error('{$title}', '{$message}', $callback)");
            die();
        }
        new TMessage($type, $message, $action, $title_msg);
    }

    private static function getFileInfo($file)
    {
        $file_name = Utils::lastStr('/', $file);
        $class_name = substr($file_name, 0, strpos($file_name, '.'));
        return $class_name;
    }
}
