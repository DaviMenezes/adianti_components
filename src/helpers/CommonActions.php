<?php

namespace Dvi\Adianti\Helpers;

use Adianti\Base\Lib\Core\AdiantiCoreApplication;
use Adianti\Base\Lib\Database\TRecord;
use Adianti\Base\Lib\Registry\TSession;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\Model\DviTRecord;
use Dvi\Adianti\Route;
use ReflectionClass;

/**
 * Helpers CommonActions
 * @Metodos compartilhados entre views e controllers
 * @package    Helpers
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait CommonActions
{
    public static function onClear($param)
    {
        $className = Reflection::shortName(get_called_class());
        TSession::setValue($className . '_form_data', null);
        TSession::setValue($className . '_filters', null);
        TSession::setValue($className . '_listOrder', null);
        TSession::setValue('method', $param['method']);

        $params = Utils::getNewParams();
        unset($params['id'], $params['key'], $params['method'], $params['static']);

        AdiantiCoreApplication::loadPage($className, null, $params);
    }

    /** * check if form has token and if is valid(session value) */
    protected function validateToken()
    {
        $called_class = Route::getClassName(get_called_class());
        $token = $this->params[$called_class . '_form_token'];
        if (!empty($token) and (
                $token === TSession::getValue($called_class.'_form_token'))) {
            return true;
        }
        return false;
    }

    protected function getModelAndAttributesOfTheForm(): array
    {
        $model_default = $this->view->getModel();

        $this->currentObj = new $model_default($this->params[Reflection::shortName($model_default) . '-id'] ?? null);

        //get result form data
        $form_data = array_merge($this->params, (array)$this->getFormData());
        unset($form_data['class'], $form_data['method'], $form_data[Reflection::shortName(get_called_class()).'_form_token']);

        /**@var DviModel $last_model*/
        $last_model = $this->currentObj;
        $model_form_attributes[$model_default] = $last_model;

        foreach ($form_data as $property => $value) {
            if (empty($value)) {
                continue;
            }

            $models = explode('-', $property);
            $property = array_pop($models);//removing property name of array

            foreach ($models as $model_name) {
                if ($model_name == $model_default) {
                    $last_model = $this->currentObj;
                    $this->setModelAttributeValue($last_model, $property, $value);
                    continue;
                }

                $relationships = $last_model->getRelationships();
                $model_name_lower = strtolower($model_name);
                if (array_key_exists($model_name_lower, $relationships)) {
                    $last_model = $last_model->$model_name_lower();
                }

                $this->setModelAttributeValue($last_model, $property, $value);

                $model_form_attributes[$model_name] = $last_model;
            }
        }
        return $model_form_attributes;
    }

    public function showErrorMsg($param)
    {
        new TMessage('error', $param['msg']);
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

    protected function formFieldModelAttributeIsDisabled(DviTRecord &$current_obj, $attribute_name)
    {
        $class_name = Reflection::shortName($current_obj);
        $formField = $this->view->getPanel()->getForm()->getField($class_name . '-' . $attribute_name);

        $method_exist = $this->hasIsDisabledMethod($formField);

        if ($method_exist and $formField->isDisabled()) {
            return true;
        }
        return false;
    }

    protected function modelSetMethodExists(DviTRecord &$current_obj, $attribute_name)
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
}
