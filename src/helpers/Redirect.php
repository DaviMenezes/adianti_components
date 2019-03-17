<?php

namespace Dvi\Adianti\Helpers;

use Adianti\Base\Lib\Widget\Base\TScript;
use App\Http\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
    /**
     * Only loads application content
     * @param string $route
     * @param array $parameters
    */
    public static function ajaxLoadPage(string $route, array $parameters = null)
    {
        $route = urlRoute($route, $parameters);

        TScript::create("__adianti_load_page('".$route."');");
    }

    /**
     * Load all application
     * @param string $route
     * @param array $parameters
    */
    public static function ajaxGoTo(string $route, array $parameters = null)
    {
        $route = urlRoute($route, $parameters);

        TScript::create("__adianti_goto_page('{$route}');");
    }

    /**
     * Send http redirect response
     * @param string $url
     * @param int $status
     * @param array $headers
     * @return RedirectResponse
     */
    public static function redirect(string $url, int $status = 302, $headers = array()): RedirectResponse
    {
        $response = new RedirectResponse($url, $status, $headers);
        $response->send();
        return $response;
    }

    /**
     * Send http redirect response to internal route
     * @param string $route ex: route('your/route/page/')
     * @param array $parameters
     * @param int $status
     * @return RedirectResponse
     */
    public static function redirectToRoute(string $route, array $parameters = array(), int $status = 302): RedirectResponse
    {
        $route = urlRoute($route, $parameters);

        return self::redirect($route, $status);
    }
}
