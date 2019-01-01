<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Control\TPage;
use Adianti\Base\Lib\Registry\TSession;
use App\Http\Request;
use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\Helpers\Utils;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\View\Standard\DviBaseView;
use Dvi\Adianti\Widget\Form\PanelGroup\PanelGroup;
use Exception;

/**
 * Trait DviControl

 * @package    control
 * @subpackage trait
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class DviControl extends TPage
{
    /**@var DviBaseView */
    public $view;
    protected $already_build_view;
    /**@var DviModel*/
    protected $currentObj;
    /**@var Request*/
    protected $request;
    protected $database = 'default';
    protected $already_get_view_content;

    use Utils;
    use Reflection;

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request = $request;
    }

    abstract protected function buildView();

    protected function getViewContent()
    {
        if ($this->already_get_view_content) {
            return;
        }
        if (isset($this->view)) {
            parent::add($this->view->getContent());
            $this->already_get_view_content = true;
        }
    }

    public function show()
    {
        ob_start();
        parent::show();
        $result = ob_get_contents();

        ob_clean();
        $response = new \stdClass();
        $response->page_title = $this->view->getPanel()->getTitle();
        $response->page_content = $result;
        $response = json_encode($response);
        return $response;
    }
}
