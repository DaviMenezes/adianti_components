<?php
namespace Dvi\Adianti\Control;

use ReflectionClass;

/**
 * Model DControl
 *
 * @version    Dvi 1.0
 * @package    control
 * @subpackage dvi
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait DControl
{
    public function formatCurrency($value)
    {
        if (is_numeric($value)) {
            return 'R$ '.number_format($value, 2, ',', '.');
        }
        return $value;
    }

    public static function getClassName($class)
    {
        try {
            return (new ReflectionClass($class))->getShortName();
        } catch (\ReflectionException $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }
}
