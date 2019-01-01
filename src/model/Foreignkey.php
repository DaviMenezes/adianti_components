<?php

namespace Dvi\Adianti\Model;

use MyCLabs\Enum\Enum;

/**
 * Helpers Foreignkey
 * @package    Helpers
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 * @see https://t.me/davimenezes
 */
class Foreignkey extends Enum
{
    public const RESTRICT = 'restrict';
    public const CASCATE = 'cascate';
    public const SET_NULL = 'set_null';
    public const NO_ACTION = 'no_action';
}
