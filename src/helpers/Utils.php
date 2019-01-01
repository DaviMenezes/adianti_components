<?php
namespace Dvi\Adianti\Helpers;

use App\Http\Request;

/**
 *  Utils
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

    public static function editing(Request $request)
    {
        $corda_uri = str($request->getRequestUri())->lastStr('xhr-');
        if ($corda_uri->contains('id')) {
            return true;
        }
        return false;
    }

    /**Dump and die*/
    public function dd($var)
    {
        var_dump($var);
        die();
    }

    //Todo remover o redirecionamento esta na classe redirect e que deve passar a um helper
    public function loadPage(array $params = null)
    {
        Redirect::loadPage($params)->go(get_called_class());
    }
    //Todo remover o redirecionamento esta na classe redirect e que deve passar a um helper
    public function goToPage(array $params = null)
    {
        Redirect::goToPage($params)->go(get_called_class());
    }

    /**Return the last string of the string with separator
     * @example 'Model-name' return 'name'
     * @param string $separator
     * @param string $string
     * @return string
     */
    public static function lastStr(string $separator, string $string):string
    {
        $position = strlen($string) - strrpos($string, $separator);
        if (strpos($string, $separator) !== false) {
            $position -= 1;
        }
        return substr($string, - $position);
    }
}
