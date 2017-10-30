<?php
namespace Dvi\Widget\Util;

use Adianti\Control\TAction;
use Adianti\Widget\Util\TActionLink;

/**
 * Model DActionLink
 *
 * @version    Dvi 1.0
 * @package    util
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DActionLink extends TActionLink
{
    public function __construct(TAction $action, string $value = null, string $icon = null, string $color = null, string $size = null, string $decoration = null)
    {
        parent::__construct($value, $action, $color, $size, $decoration, $icon);
    }
}