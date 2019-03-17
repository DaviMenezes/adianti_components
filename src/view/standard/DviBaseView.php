<?php

namespace Dvi\Adianti\View\Standard;

use Adianti\Base\Lib\Registry\TSession;
use App\Http\Request;
use Dvi\Adianti\Helpers\GUID;
use Dvi\Adianti\Helpers\Utils;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\Widget\Container\VBox;

/**
 * View DviBaseView
 *
 * @package    View
 * @subpackage Adianti
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class DviBaseView
{
    /**@var VBox */
    protected $vbox;
    /**@var DviModel*/
    protected $model;
    /**@var \App\Http\Request*/
    protected $request;

    use Utils;
    use GUID;

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->setVBox();
    }

    abstract public function build(Request $request);

    abstract public function getContent();

    /** @example $this->panel->setTitle('My Page title'); */
    abstract public function setPageTitle();

    /** @example $this->model = MyModel::class; */
    abstract public function setModel();

    /** @example $this->fields([
     *      ['field1', 'field2'],
     *      ['modelX.field4', 'modeldY.field2', 'modelZ.field3']
     * ]);
     */
    abstract protected function setStructureFields();

    /**@return DviModel */
    public function getModel()
    {
        if ($this->model) {
            return $this->model;
        }
        $this->setModel();

        return $this->model;
    }

    protected function setVBox(): void
    {
        if ($this->vbox) {
            return;
        }
        $this->vbox = new VBox();
    }
}
