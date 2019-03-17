<?php

namespace Dvi\Adianti\View\Standard\Form;

use Adianti\Base\Lib\Registry\TSession;
use App\Http\Request;
use Dvi\Adianti\Helpers\GUID;
use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\View\Standard\DviBaseView;
use Dvi\Adianti\View\Standard\GroupFieldView;
use Dvi\Adianti\Widget\Form\Field\Hidden;
use Dvi\Adianti\Widget\Form\PanelGroup\PanelGroup;

/**
 * Form BaseFormView
 *
 * @package    Form
 * @subpackage Standard
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class BaseFormView extends DviBaseView
{
    /**@var PanelGroup */
    protected $panel;
    protected $panel_created;
    protected $groupFields = array();

    public function createPanel(Request $request)
    {
        try {
            if ($this->panel_created) {
                return;
            }
            $this->createPanelForm();

            $this->createFormToken($request);

            $this->buildFields();

            $this->createPanelFields();

            $this->panel_created = true;
        } catch (\Exception $e) {
            throw new \Exception('Criação do painel.'.$e->getMessage());
        }
    }

    public function createPanelForm()
    {
        $this->panel = $this->panel ?? new PanelGroup($this->request->routeInfo()->fullRoute());

        $this->setPageTitle();

        $this->setFormNameInSession();
    }

    public function setFormNameInSession()
    {
        $class_name = Reflection::shortName(Request::instance()->routeInfo()->class());
        TSession::setValue($class_name . '_form_name', $this->panel->getForm()->getName());
    }

    public function createFormToken(Request $request)
    {
        if ($this->panel->getForm()->getField('form_token')) {
            return;
        }
        $model_short_name = Reflection::shortName($this->model);
        $field_id = new Hidden($model_short_name . '-id');

        $field_id->setValue($this->request->get('id') ?? $this->request->get($model_short_name.'-id'));
        $field_token = new Hidden('form_token');

        $token = $request->get('form_token');
        if (!$request->has('form_token')) {
            $token = GUID::getID();
            TSession::setValue('form_token', $token);
        }
        $field_token->setValue($token);

        $this->panel->addHiddenFields([$field_id, $field_token]);
    }

    public function getPanel()
    {
        return $this->panel;
    }

    public function getGroupFields()
    {
        return $this->groupFields;
    }

    protected function fields(array $fields): GroupFieldView
    {
        $this->groupFields[] = $group = (new GroupFieldView())->fields($fields);

        return $group;
    }

    protected function modelProperties($class = null)
    {
        $model = $class ?? $this->model;
        return Reflection::getPublicPropertyNames(new $model);
    }

    abstract public function createPanelFields();
}
