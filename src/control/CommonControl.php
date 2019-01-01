<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Registry\TSession;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Adianti\Base\Lib\Widget\Dialog\TQuestion;
use App\Http\Request;
use Dvi\Adianti\Database\Transaction;
use Dvi\Adianti\Helpers\Redirect;
use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\Helpers\Utils;
use Dvi\Adianti\Model\ActiveRecord;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\View\Standard\DviBaseView;
use Dvi\Adianti\Widget\Util\Action;
use ReflectionClass;

/**
 * Control CommonControl
 * @Metodos compartilhados entre views e controllers
 * @package    Control
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait CommonControl
{
    /**@var \App\Http\Request*/
    protected $request;

    public static function onClear(Request $request)
    {
        $className = Reflection::shortName($request->routeInfo()->class());

        TSession::setValue($className . '_form_data', null);
        TSession::setValue($className . '_filters', null);
        TSession::setValue($className . '_listOrder', null);

        //Todo check need params maybe in url pagination...
        $params = $request->getCollection()->except(['id', 'key', 'static'])->all();

        $route = $request->routeInfo()->fullRoute()->removeRight('clear')->removeRight('/');

        $parent_class = get_parent_class(get_called_class());
        if ($parent_class == FormControl::class) {
            $route = $route->removeRight('clear')->ensureRight('create')->str();
            Redirect::ajaxLoadPage($route);
            return;
        }

        Redirect::redirect(urlRoute($route->str()));
    }

    /** check if form has token and if is valid(session value) */
    protected function validateToken()
    {
        $token = $this->request->get('form_token');
        if (!empty($token) and ($token === TSession::getValue('form_token'))) {
            return true;
        }
        return false;
    }

    protected function getModelAndAttributesOfTheForm(): array
    {
        $model_default = $this->view->getModel();

        $this->currentObj = new $model_default($this->request->get(Reflection::shortName($model_default) . '-id'));

        $form_data = $this->request->getCollection()
            ->except(['form_token'])
            ->merge((array)$this->getFormData())
            ->all();

        /**@var DviModel $last_model */
        $last_model = $this->currentObj;
        $model_form_attributes[Reflection::shortName($model_default)] = $last_model;

        foreach ($form_data as $property => $value) {
            $models = explode('-', $property);
            $property = Utils::lastStr('-', $property);

            foreach ($models as $model_name) {
                if ($model_name == Reflection::shortName($model_default)) {
                    $last_model = $this->currentObj;
                    $this->setModelAttributeValue($last_model, $property, $value);
                    continue;
                }

                $model_name_lower = strtolower($model_name);
                if ($last_model->getRelationship($model_name_lower)) {
                    $last_model = $last_model->$model_name_lower();
                }
                $this->setModelAttributeValue($last_model, $property, $value);

                $model_form_attributes[$model_name] = $last_model;
            }
        }
        return $model_form_attributes;
    }

    public static function showErrorMsg(Request $request)
    {
        $msg = TSession::getValue($request->get('form') . $request->get('field'));
        new TMessage('error', urldecode($msg ?? 'Mensagem não encontrada'));
    }

    public function getFormData()
    {
        $data = $this->view->getPanel()->getFormData();
        $valid_data = new \stdClass();
        foreach ((array)$data as $prop => $value) {
            if (empty($value) or is_array($prop) or strpos($prop, 'btn_') !== false) {
                continue;
            }
            $valid_data->$prop = $this->prepareFormFieldData($prop, $value);
        }
        return $valid_data;
    }

    /** apply filters in form data*/
    public function prepareFormFieldData($prop, $value)
    {
        return htmlspecialchars($value);
    }

    protected function setModelAttributeValue(DviModel &$current_obj, $attribute_name, $value)
    {
        if (!in_array($attribute_name, $current_obj->getAttributes())) {
            return;
        }

        if ($this->formFieldModelAttributeIsDisabled($current_obj, $attribute_name)) {
            return;
        }

        if ($this->modelSetMethodExists($current_obj, $attribute_name)) {
            $set_attibute_method = 'set_' . $attribute_name;
            $current_obj->$set_attibute_method($value);
            return;
        }
        $current_obj->addAttributeValue($attribute_name, $value);
    }

    protected function formFieldModelAttributeIsDisabled(ActiveRecord &$current_obj, $attribute_name)
    {
        $class_name = Reflection::shortName($current_obj);
        $formField = $this->view->getPanel()->getForm()->getField($class_name . '-' . $attribute_name);

        $method_exist = $this->hasIsDisabledMethod($formField);

        if ($method_exist and $formField->isDisabled()) {
            return true;
        }
        return false;
    }

    protected function modelSetMethodExists(ActiveRecord &$current_obj, $attribute_name)
    {
        $methods = get_class_methods(get_class($current_obj));
        if (in_array('set_' . $attribute_name, $methods)) {
            return true;
        }
        return false;
    }

    protected function hasIsDisabledMethod($formField): bool
    {
        return in_array('isDisabled', (new ReflectionClass(get_class($formField)))->getMethods());
    }

    public static function onDelete(Request $request)
    {
        $route_base = $request->attr('route_base');

        $id = $request->get('id');
        $route_base = $route_base . '/delete/confirm';
        $action_yes = new Action($route_base);

        $url_params = $request->obj()->query->all();
//Todo check        $url_params['static'] = 1;
        $url_params['key'] = $id;
        $url_params['id'] = $id;

        $action_yes->setRouteParams(collection($url_params)->route());

        $request->add(['url_params' => $url_params]);

        $action_yes->setParameters($url_params);
        $action_yes->setStatic();

        new TQuestion(_t('Do you really want to delete ?'), $action_yes);
    }

    public static function delete(Request $request)
    {
        try {
            Transaction::open();

            $view = $request->attr('view_class');
            /**@var DviBaseView $view*/
            $view = new $view($request);

            /**@var DviModel $model*/
            $model = $view->getModel();
            $model = new $model();
            $modelShortName = Reflection::shortName($model);
            $model->delete($request->get('id') ?? $request->get($modelShortName .'-id'));

            Transaction::close();

            self::onBack($request);
        } catch (\Exception $e) {
            Transaction::rollback();
            throw $e;
        }
    }

    public static function onBack(Request $request)
    {
        $url_params = collect($request->attr('url_params'))->except(['id', 'key', 'static'])->all();

        $route = urlRoute($request->attr('route_base'));
        Redirect::ajaxLoadPage($route, $url_params);
    }
}
