<?php
/**
 * Column to bootstrap grid
 *
 * @version    Adianti 4.0
 * @package    grid bootstrap
 * @subpackage base
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */

namespace Dvi\Widget\Base;

use Adianti\Widget\Datagrid\TDataGridColumn;

class DataGridColumn extends TDataGridColumn
{
    public $width;

    public function __construct($name, $label, $align, $width = null)
    {
        parent::__construct($name, $label, $align, $width);
    }



}
