<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Registry\TSession;
use App\Http\Request;
use Dvi\Adianti\Database\Transaction;
use Dvi\Adianti\View\Standard\Form\FormView;
use mysql_xdevapi\Exception;

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
    /**@var FormView */
    public $view;

    use FormControlTrait;
    use CommonControl;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->init();
    }

    /**@example
     * $this->request->add(['view_class' => MyFormListView::class]);
     * $this->request->add(['route_page_list' => urlRoute('/route')]);
     */
    abstract public function init();

    protected function createView()
    {
        $view = $this->request->attr('view_class');
        $this->view = new $view($this->request);
    }

    protected function validateRequiredParams()
    {
        $msg_error = null;
        if (!$this->request->attr('route_base')) {
            throw new \Exception('Informe o atributo route_base');
        }

        if (!$this->request->attr('view_class')) {
            $msg_error .= 'Defina o parâmetro viewClass no método init() do seu controlador (' . self::shortName(get_called_class()) . ')' . "<br>";
        }
        if (!is_subclass_of($this->request->attr('view_class'), FormView::class)) {
            $msg_error .= 'A view deve ser filha de ' . (new \ReflectionClass(FormView::class))->getShortName();
        }

        if (!$this->request->attr('route_page_list')) {
            $msg_error .= 'Defina o parâmetro route_page_list.';
            $msg_error .= 'Ela representa o controlador de listagem e será usada por alguns componentes.';
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
        try {
            Transaction::open();

            $this->init();

            $this->validateRequiredParams();

            $this->createView();

            $this->createCurrentObject();

            $this->view->setCurrentObj($this->currentObj);

            $this->view->build($this->request);
            Transaction::close();
        } catch (\Exception $e) {
            Transaction::rollback();
            throw $e;
        }
    }

    public function create()
    {
        $this->buildView($this->request);
        $this->getViewContent();
    }
}
