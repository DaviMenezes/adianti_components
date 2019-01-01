<?php

namespace Dvi\Adianti\View\Standard\Form;

use App\Http\Request;
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
    /**@var PanelGroup */
    protected $panel;

    public function buildForm(Request $request)
    {
        $this->createPanelForm();

        $this->createFormToken($request);

        if (!$this->alreadyCreatePanelRows()) {
            $this->buildFields();
            $this->createPanelFields();
        }
        $this->createActions();
    }

    public function createActionSave($route = null)
    {
        $route = $route ?? urlRoute($this->request->attr('route_base').'/save');
        //Todo check need especific button_save, then remove if not used
        $this->button_save = $this->panel->addActionSave($route);
        return $this->button_save;
    }
}
