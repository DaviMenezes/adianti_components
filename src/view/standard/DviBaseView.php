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
    /**@var PanelGroup $panel */
    protected $panel;
    protected $groupFields = array();
    protected $params;

    use Utils;
    use GUID;

    public function __construct($param)
    {
        $this->params = $param;
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

    public function createPanelForm()
    {
        $this->panel = $this->panel ?? new PanelGroup($this->params['class']);
        $this->setPageTitle();
    }

    public function createFormToken($param)
    {
        if ($this->panel->getForm()->getField($param['class'] . '_form_token')) {
            return;
        }
        $field_id = new Hidden(Reflection::shortName($this->model) . '-id');
        $field_id->setValue($this->params['id'] ?? null);
        $field_token = new Hidden($param['class'] . '_form_token');

        $token = $param[$param['class'] . '_form_token'] ?? null;
        if (empty($param[$param['class'] . '_form_token'])) {
            $token = GUID::getID();
            TSession::setValue($param['class'] . '_form_token', $token);
        }
        $field_token->setValue($token);

        $this->panel->addHiddenFields([$field_id, $field_token]);
    }

    public function getPanel()
    {
        return $this->panel;
    }

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

    private function getPublicModelProperties($class = null)
    {
        $model = $class ?? $this->getModel();

        return Reflection::getPublicModelPropertyNames(new $model);
    }

    protected function modelProperties()
    {
        return $this->getPublicModelProperties();
    }
}
