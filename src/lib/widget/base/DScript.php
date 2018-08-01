<?php

namespace Dvi\Lib\Widget\Base;

use Adianti\Base\Lib\Widget\Base\TScript;

/**
 * Widget DScript
 *
 * @package    Widget
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DScript extends TScript
{
    private static $scripts_id = array();

    public static function add(string $id, string $code, bool $show = true)
    {
        if (!in_array($id, self::$scripts_id)) {
            self::$scripts_id[] = $id;
            return parent::create($code, $show);
        }
    }
}
