<?php

namespace Dvi\Adianti\Control;

use Dvi\Adianti\Database\Transaction;
use Dvi\Adianti\Helpers\CommonActions;
use Dvi\Adianti\View\Standard\Form\FormView;

/**
 * Control FormControl
 *
 * @package    Control
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class FormControl extends DviControl
{
    protected $viewClass;
    /**@var FormView $view */
    protected $view;

    use FormControlTrait;
    use CommonActions;

    public function __construct($param)
    {
        try {
            Transaction::open();

            parent::__construct($param);

            $this->init();

            $this->validateRequiredParams();

            $this->createView($param);

            $this->createCurrentObject();

            $this->view->setCurrentObj($this->currentObj);

            $this->view->setPageList($this->pageList);

            Transaction::close();
        } catch (\Exception $e) {
            Transaction::rollback();
            throw $e;
        }
    }

    /**@example
     * $this->viewClass = MyFormListView::class; (Representante da view)
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
            $msg_error .= 'Defina a propriedade viewClass no método init() do seu controlador (' . self::shortName(get_called_class()) . ')' . "<br>";
        }
        if (!is_subclass_of($this->viewClass, FormView::class)) {
            $msg_error .= 'A view deve ser filha de ' . (new \ReflectionClass(FormView::class))->getShortName();
        }

        if (empty($this->pageList)) {
            $msg_error .= 'Defina a propriedade pageList.';
            $msg_error .= 'Ela representa o controlador de listagem e será usada por alguns componentes.';
        } else {
            if (!is_subclass_of($this->pageList, ListControl::class)) {
                $msg_error = 'A sua listagem deve ser do tipo ' . (new \ReflectionClass(ListControl::class))->getShortName();
            }
        }

        if ($msg_error) {
            if (ENVIRONMENT == 'development') {
                $msg = 'O método init() é responsável em coletar algumas informações importantes ';
                $msg .= ' para o bom funcionamento do sistema.';
                $msg .= ' Verifique as mensagens a seguir para corrigir o problema ';
                $msg .= $msg_error;
                throw new \Exception($msg);
            }
            throw new \Exception('Não foi possível criar a tela. Contate o administrador');
        }
    }

    protected function buildView()
    {
        $this->view->build($this->params);
    }

    public function show()
    {
        if (!$this->hasMethod($this->params)) {
            $this->buildView();
            $this->getViewContent();
        }
        parent::show();
    }
}
