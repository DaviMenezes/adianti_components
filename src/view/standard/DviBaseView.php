<?php

namespace Dvi\Adianti\View\Standard;

use Adianti\Base\Lib\Registry\TSession;
use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\Helpers\Utils;
use Dvi\Adianti\Helpers\GUID;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\Widget\Form\DHidden;
use Dvi\Adianti\Widget\Form\PanelGroup\DviPanelGroup;

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
    protected $model;
    /**@var DviPanelGroup $panel*/
    protected $panel;
    protected $groupFields = array();
    protected $params;

    use Utils;
    use GUID;

    public function __construct($param)
    {
        $this->params = $param;
    }

    abstract public function build($param);

    abstract public function getContent();

    abstract public function setPageTitle();

    abstract protected function setModel();

    abstract protected function setStructureFields();

    public function createPanelForm()
    {
        $this->panel = $this->panel ?? new DviPanelGroup($this->params['class']);
        $this->setPageTitle();
    }

    public function createFormToken($param)
    {
        if ($this->panel->getForm()->getField($param['class'] . '_form_token')) {
            return;
        }
        $field_id = new DHidden('id');
        $field_id->setValue($this->params['id'] ?? null);
        $field_token = new DHidden($param['class'].'_form_token');

        $token = $param[$param['class'].'_form_token'] ?? null;
        if (empty($param[$param['class'].'_form_token'])) {
            $token = GUID::getID();
            TSession::setValue($param['class'].'_form_token', $token);
        }
        $field_token->setValue($token);

        $this->panel->addHiddenFields([$field_id, $field_token]);
    }

    public function getPanel()
    {
        return $this->panel;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getGroupFields()
    {
        return $this->groupFields;
    }

    protected function fields($fields)
    {
        $group = new GroupFieldView();
        $group->fields($fields);
        $this->groupFields[] = $group;

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
