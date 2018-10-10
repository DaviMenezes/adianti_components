<?php
/**
 * Created by PhpStorm.
 * User: davi
 * Date: 17/07/18
 * Time: 20:11
 */

namespace Dvi\Adianti\Control;

interface StandardSearchListInterface
{
    public function onSearch();

    public static function onClear($param);
}
