<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Base\TScript;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Adianti\Base\Lib\Widget\Form\TButton;
use Dvi\Adianti\Database\DTransaction;
use Dvi\Adianti\Widget\Form\DviPanelGroup;

/**
 * Control DviStandardForm
 *
 * @version    Dvi 1.0
 * @package    Control
 * @subpackage component
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DviStandardForm extends DviControl
{
    protected $objectClass;
    /**@var DviPanelGroup $panel*/
    protected $panel;
    /**@var TAction $button_save*/
    private $button_save;
    /**@var TButton $button_clear*/
    private $button_clear;

    use DviTPageForm;

    public function __construct($param)
    {
        try {
            DTransaction::open();

            parent::__construct();

            $this->createCurrentObject($param);

            $this->createPanelForm($param);

            $this->mountModelFields($param);

            $this->createActions();

            parent::add($this->panel);

            $this->cancelEnterSubmit();

            DTransaction::close();
        } catch (\Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function createActions()
    {
        $this->createActionSave();

        $this->createActionClear();
    }

    protected function getButtonSave()
    {
        return $this->button_save;
    }

    protected function getButtonClear()
    {
        return $this->button_clear;
    }

    private function cancelEnterSubmit()
    {
        TScript::create('$("input, select, text").keypress(function (e) {
            var code = null;
            code = (e.keyCode ? e.keyCode : e.which);                
            return (code == 13) ? false : true;
        });');
    }
}
