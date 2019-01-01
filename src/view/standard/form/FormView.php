<?php

namespace Dvi\Adianti\View\Standard\Form;

use Adianti\Base\Lib\Registry\TSession;
use Adianti\Base\Lib\Widget\Base\TScript;
use App\Http\Request;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\View\Standard\PageFormView;
use Dvi\Adianti\Widget\Form\Button;

/**
 * Control DviStandardForm
 * @package    Control
 * @subpackage component
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class FormView extends BaseFormView
{
    /**@var Button */
    protected $button_save;
    /**@var DviModel */
    protected $currentObj;

    use PageFormView;
    use FormViewTrait;

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function build(Request $request)
    {
        try {
            $this->setModel();

            $this->setStructureFields();

            $this->createPanelForm();

            $this->createFormToken($request);

            $this->createPanelFields();

            $this->createActions();

//            $this->cancelEnterSubmit();

            return $this;
        } catch (\Exception $e) {
            throw new \Exception('Erro ao construir a tela. Erro: ' . $e->getMessage());
        }
    }

    public function createActions()
    {
        $this->createActionGoBack();

        $this->createActionSave();

        $this->createActionClear();

        $this->createActionDelete();
    }

    public function getButtonSave()
    {
        return $this->button_save;
    }

    private function cancelEnterSubmit()
    {
        TScript::create('$("input, select, text").keypress(function (e) {
            var code = null;
            code = (e.keyCode ? e.keyCode : e.which);                
            return (code == 13) ? false : true;
        });');
    }

    public function getContent()
    {
        return $this->getPanel();
    }

    public function setCurrentObj($obj)
    {
        $this->currentObj = $obj;
    }
}
