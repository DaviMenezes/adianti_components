<?php

namespace Dvi\Adianti\Control;

use Dvi\Adianti\Helpers\Utils;

/**
 * Control PaginationHelper
 *
 * @package    Control
 * @subpackage DviComponents
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait PaginationHelper
{
    public static function getUrlPaginationParameters($param): array
    {
        if (self::callCameFromAnotherClass($param)) {
            unset($param['class']);
            return $param;
        }

        $new_url_params = Utils::getNewParams();

        return $new_url_params;
    }

    protected static function callCameFromAnotherClass($param):bool
    {
        $url_params = explode('&', $_SERVER['QUERY_STRING']);
        $class = explode('=', $url_params[0]);

        if ($class[1] !== $param['class']) {
            return true;
        }
        return false;
    }
}
