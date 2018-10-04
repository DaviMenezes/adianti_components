<?php
namespace Dvi\Adianti\Helpers;

/**
 * Model Utils
 *
 * @version    Dvi 1.0
 * @package    control
 * @subpackage dvi
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait Utils
{
    public function formatCurrency($value, $decimals = 2)
    {
        if (is_numeric($value)) {
            return 'R$ '.number_format($value, $decimals, ',', '.');
        }
        return $value;
    }

    protected function isEditing()
    {
        return self::editing($this->request);
    }

    public static function editing($params)
    {
        if ((!empty($params['id']) and $params['id'] != 0)) {
            return true;
        }
        return false;
    }

    public function dd($var)
    {
        var_dump($var);
        die();
    }

    public static function getNewParams():array
    {
        $new_params = array();

        $url_params = explode('&', $_SERVER['QUERY_STRING']);
        unset($url_params[0]);
        foreach ($url_params as $url_param) {
            if (!empty($url_param)) {
                $value = explode('=', $url_param);
                $new_params[$value[0]] = $value[1];
            }
        }

        return $new_params;
    }

    public function loadPage(array $params = null)
    {
        Redirect::loadPage($params)->go(get_called_class());
    }

    public function goToPage(array $params = null)
    {
        Redirect::goToPage($params)->go(get_called_class());
    }
}
