<?php

namespace Dvi\Adianti\View\Standard;

use Adianti\Base\Lib\Registry\TSession;
use Dvi\Adianti\Helpers\GUID;
use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\Helpers\Utils;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\Widget\Container\VBox;
use Dvi\Adianti\Widget\Form\Field\Hidden;
use Dvi\Adianti\Widget\Form\PanelGroup\PanelGroup;

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
    /**@var VBox $vbox */
    protected $vbox;
    protected $model;
    protected $groupFields = array();
    protected $request;

    use Utils;
    use GUID;

    public function __construct($param)
    {
        $this->request = $param;
        $this->vbox = new VBox();
    }

    abstract public function build($param);

    abstract public function getContent();

    /** @example $this->panel->setTitle('My Page title'); */
    abstract public function setPageTitle();

    /** @example $this->model = MyModel::class; */
    abstract protected function setModel();

    /** @example $this->fields([
     *      ['field1', 'field2'],
     *      ['modelX.field4', 'modeldY.field2', 'modelZ.field3']
     * ]);
     */
    abstract protected function setStructureFields();

    /**@return DviModel */
    public function getModel()
    {
        return $this->model;
    }

    public function getGroupFields()
    {
        return $this->groupFields;
    }

    protected function fields(array $fields)
    {
        $this->groupFields[] = $group = (new GroupFieldView())->fields($fields);

        return $group;
    }
}
