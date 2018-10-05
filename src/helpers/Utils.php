<?php
namespace Dvi\Adianti\Helpers;

/**
 *  Utils
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

    public function isEditing()
    {
        return self::editing($this->request);
    }

    public static function editing($params)
    {
        foreach ($params as $item => $value) {
            if (in_array($item, ['class', 'method', 'form_token'])) {
                continue;
            }
            $array = explode('-', $item);
            $property = array_pop($array);

            if (in_array($property, ['id', 'key']) and !empty($value) and $value != 0) {
                return true;
            }
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
