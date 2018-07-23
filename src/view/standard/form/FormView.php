<?php

namespace Dvi\Adianti\View\Standard\Form;

use Dvi\Adianti\Widget\Form\PanelGroup\DviPanelGroup;

/**
 * Components PageForm
 *
 * @package    Components
 * @subpackage Dvi
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait FormView
{
    /**@var DviPanelGroup $this->panel*/
    protected $panel;

    public function createActionSave()
    {
        return $this->panel->addActionSave();
        $this->button_save = $this->panel->getCurrentButton();
        return $this->button_save;
    }
}
