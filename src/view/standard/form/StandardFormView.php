<?php

namespace Dvi\Adianti\View\Standard\Form;

use Adianti\Base\Lib\Widget\Base\TScript;
use Dvi\Adianti\Database\Transaction;
use Dvi\Adianti\Helpers\Utils;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\View\Standard\DviBaseView;
use Dvi\Adianti\View\Standard\PageFormView;
use Dvi\Adianti\Widget\Form\Button;

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
abstract class StandardFormView extends DviBaseView
{
    /**@var Button $button_save*/
    protected $button_save;
    /**@var Button $button_clear*/
    protected $button_clear;
    /**@var DviModel $currentObj*/
    protected $currentObj;
    protected $pageList;

    use PageFormView;
    use Utils;
    use FormView;

    public function __construct($param)
    {
        parent::__construct($param);

        $this->setModel();
        $this->setStructureFields();
    }

    public function build($param)
    {
        try {
            Transaction::open();

            $this->createPanelForm();

            $this->createFormToken($param);

            $this->createPanelFields();

            $this->createActions();

            $this->cancelEnterSubmit();

            Transaction::close();

            return $this;
        } catch (\Exception $e) {
            Transaction::rollback();
            throw new \Exception('Erro ao construir a tela. Erro: '. $e->getMessage());
        }
    }

    public function createActions()
    {
        $this->createActionGoBack();

        $this->createActionSave();

        $this->createActionClear();
    }

    public function getButtonSave()
    {
        return $this->button_save;
    }

    public function getButtonClear()
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

    public function getContent()
    {
        return $this->getPanel();
    }

    public function setCurrentObj($obj)
    {
        $this->currentObj = $obj;
    }

    public function setPageList($pagelist)
    {
        $this->pageList = $pagelist;
    }
}
