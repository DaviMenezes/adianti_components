<?php

namespace Dvi\Adianti\View\Standard\Form;

use Dvi\Adianti\Widget\Form\PanelGroup\PanelGroup;

/**
 * Components PageForm
 *
 * @package    Components
 * @subpackage Dvi
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait FormViewTrait
{
    /**@var PanelGroup $this->panel*/
    protected $panel;

    public function buildForm($param)
    {
        $this->createPanelForm();

        $this->createFormToken($param);

        if (!$this->alreadyCreatePanelRows()) {
            $this->buildFields();
            $this->createPanelFields();
        }
        $this->createActions();
    }

    public function createActionSave()
    {
        $this->panel->addActionSave();
        $this->button_save = $this->panel->getCurrentButton();
        return $this->button_save;
    }
}
