<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Dvi\Adianti\Database\DTransaction;
use Dvi\Adianti\Helpers\CommonActions;
use Dvi\Adianti\Route;
use Dvi\Adianti\Widget\Dialog\DMessage;

/**
 * Control StandardFormControl
 *
 * @package    Control
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class StandardFormControl extends DviControl
{
    protected $viewClass;

    use FormControl;
    use CommonActions;

    public function __construct($param)
    {
        try {
            DTransaction::open();

            parent::__construct($param);

            $this->init();

            $this->validateRequiredParams();

            $this->createView($param);

            $this->createCurrentObject();

            $this->view->setCurrentObj($this->currentObj);
            $this->view->setPageList($this->pageList);

            DTransaction::close();
        } catch (\Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
            die();
        }
    }

    /**@example
     * $this->viewClass = MyFormListView::class; (Representande da view)
     * $this->pageList = MyListControl::class (Controlador representante da listagem);
     */
    abstract public function init();

    protected function createView($param)
    {
        $this->view = new $this->viewClass($param);
    }

    protected function validateRequiredParams()
    {
        $msg_error = null;
        if (empty($this->viewClass)) {
            $msg_error .= 'Defina a propriedade viewClass no método init() do seu controlador ('.self::getClassName(get_called_class()).')'."<br>";
        }
        $this->setPageList();
        if (empty($this->pageList)) {
            $msg_error .= 'Defina a propriedade pageList. <br>
            Ela representa o controlador de listagem e será usada por alguns componentes.'."<br>";
        }

        if ($msg_error) {
            $msg  = 'O método init() é responsável em coletar algumas informações importantes ';
            $msg .= '<br> para o bom funcionamento do sistema.';
            $msg .= ' Verifique as mensagens abaixo para corrigir o problema <br><hr>';
            $msg .= $msg_error;
            DMessage::create('die', 'Não foi possível criar a tela. Contate o administrador', $msg);
        }
    }

    protected function buildView($params)
    {
        $this->view->build($params);

        parent::add($this->view->getContent());
    }

    public function show()
    {
        if (!$this->hasMethod(func_get_arg(0))) {
            $this->buildView($this->params);
        }
        return parent::show(func_get_arg(0));
    }

    private function setPageList()
    {
        $class_name = Route::getClassName(get_called_class());
        $array = explode('\\', get_called_class());
        array_pop($array);
        $called_class_path = implode('/', $array);
        $class_list = $called_class_path.'/'.$class_name.'List';
        if (class_exists($class_list)) {
            $this->pageList = $class_list;
        }
    }
}
