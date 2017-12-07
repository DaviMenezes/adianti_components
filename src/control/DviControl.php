<?php

namespace Dvi\Adianti\Control;

use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;
use Dvi\Adianti\Widget\Form\DviPanelGroup;
use Exception;

/**
 * Trait DviControl
 *
 * @version    Dvi 1.0
 * @package    control
 * @subpackage trait
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DviControl extends TPage
{
    use DControl;

    /**@var DviPanelGroup $panel*/
    protected $panel;
    protected $database = 'default';

    protected $grid_loaded =  false;

    public function onClear()
    {
        $this->panel->getForm()->clear();
        AdiantiCoreApplication::loadPage(get_called_class());
    }

    public function load()
    {
        AdiantiCoreApplication::loadPage(get_called_class());
    }


}
