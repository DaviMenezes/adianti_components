<?php
namespace Dvi\Adianti\Control;

/**
 * Model DControl
 *
 * @version    Dvi 1.0
 * @package    control
 * @subpackage dvi
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
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
}