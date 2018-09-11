<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Core\AdiantiCoreApplication;
use Adianti\Base\Lib\Database\TRecord;
use Adianti\Base\Lib\Database\TTransaction;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Dvi\Adianti\Database\DTransaction;
use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\Helpers\Utils;
use Dvi\Adianti\Model\DBFormFieldPrepare;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\Model\DviTRecord;
use Dvi\Adianti\View\Standard\Form\BaseFormView;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField as IFormField;
use Dvi\Adianti\Widget\Form\Field\Contract\FormFieldValidation;
use Dvi\Adianti\Widget\Form\Field\FormField;
use ReflectionClass;

/**
 * Control FormControl
 *
 * @package    Control
 * @subpackage Adianti
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait FormControl
{
    /**@var BaseFormView $view*/
    protected $view;

    public function onSave()
    {
        try {
            DTransaction::open();

            $this->beforeSave();

            $this->save();

            $this->afterSave();

            DTransaction::close();
        } catch (\Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onEdit()
    {
        try {
            $this->view->build($this->params);

            if (isset($this->params['tab']) and $this->params['tab']) {
                $this->view->getPanel()->setCurrentNotebookPage($this->params['tab']);
            }

            if (isset($this->params['id'])) {
                TTransaction::open($this->database);

                $model_alias = Reflection::getClassName($this->view->getModel());

                $query = new DBFormFieldPrepare($this->view->getModel());
                $query->mountQueryByFields($this->getFormFieldReferenceNames());
                $query->where($model_alias.'.id', '=', $this->currentObj->id);
                $result = $query->getObject();

                $formFieldNames = $this->view->getPanel()->getForm()->getFields();
                $fieldNames = array();
                foreach ($formFieldNames as $formFieldName) {
                    if (!method_exists($formFieldName, 'getReferenceName')) {
                        continue;
                    }
                    /**@var FormField $formFieldName*/
                    $referenceName = $formFieldName->getReferenceName();
                    if (!empty($referenceName)) {
                        $formFieldName->setValue($result->$referenceName);
                        $fieldNames[$referenceName] = $formFieldName->getName();
                    }
                }

                $this->getViewContent();

                TTransaction::close();
            }
        } catch (\Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    protected function beforeSave()
    {
        if (!$this->validateToken()) {
            throw new \Exception('Ação não permitida');
        }

        $this->view->createPanelForm($this->params);
        $this->view->buildFields();

        $fields = $this->view->getBuildFields();
        $error_message = array();

        foreach ($fields as $field) {
            $field->setValue($this->params[$field->getName()]);

            if (!in_array(IFormField::class, class_implements($field))) {
                continue;
            }
            /**@var FormFieldValidation $field*/
            if (!$field->validating()) {
                $error_message[] = [
                    'msg' => $field->getErrorValidation(),
                    /**@var FormField $field*/
                    'field_name' => $field->getLabel()
                ];
            }
        }

        if (count($error_message)) {
            $this->view->getPanel()->useLabelFields(true);
        }

        $this->view->createPanelFields();
        $this->buildView();

        $traits = (new ReflectionClass(self::class))->getTraitNames();
        if (in_array(ListActionsControl::class, array_values($traits))) {
            $this->onReload();
        }

        if (count($error_message)) {
            throw new \Exception('Verifique os campos em destaque');
        }
    }

    protected function save()
    {
        DTransaction::open();

        $this->view->getPanel()->keepFormLoaded();
        $model_class = $this->view->getModel();

        $this->currentObj = new $model_class($this->params['id'] ?? null);

        /**@var DviModel $this->currentObj */
        $this->currentObj->addAttribute('id');

        $models_to_save = array();
        $models = $this->getModelsToSave($models_to_save);

        $last_model = null;
        foreach ($models as $key => $instance_object) {
            $model = $instance_object['model'];

            $this->fillModelsWithAttributeValues($instance_object['attributes'], $model);

            if (!$this->isEditing() and $key > 0) {
                $fk_name = $instance_object['foreing_key']['name'];
                $model->$fk_name = $last_model->id;
            }

            $this->prepareObjBeforeSave($model, $last_model);

            $model->store();

            $last_model = $model;
        }
        DTransaction::close();
    }

    /**
     * Used by child classes to customize the object before save
    */
    protected function prepareObjBeforeSave(DviModel &$obj, $last_obj)
    {
    }

    protected function afterSave()
    {
        $new_params = Utils::getNewParams();
        unset($new_params['method']);
        $new_params['id'] = $this->currentObj->id;

        $class = Reflection::getClassName(get_called_class());
        AdiantiCoreApplication::loadPage($class, 'onEdit', $new_params);
    }

    protected function setFormWithParams()
    {
        $object = new \stdClass();
        foreach ($this->params as $key => $value) {
            $object->$key = $value;
        }
        $this->view->getPanel()->setFormData($object);
    }

    /**
     * @param $attributes
     * @param TRecord $current_obj
     * @throws \Exception
     */
    protected function fillModelsWithAttributeValues($attributes, TRecord &$current_obj)
    {
        $obj_attributes = $current_obj->getAttributes();

        foreach ($attributes as $attribute_name => $value) {
            if (in_array($attribute_name, array_keys($obj_attributes))) {
                $class_name = Reflection::getClassName($current_obj);
                $formField = $this->view->getPanel()->getForm()->getField($class_name.'-'.$attribute_name);

                $method_exist = $this->hasIsDisabledMethod($formField);

                if ($method_exist and $formField->isDisabled()) {
                    continue;
                }
                $this->setAttributeValue($current_obj, $attribute_name, $value);
            }
        }
    }

    protected function hasIsDisabledMethod($formField): bool
    {
        try {
            return in_array('isDisabled', (new ReflectionClass(get_class($formField)))->getMethods());
        } catch (\ReflectionException $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    protected function setAttributeValue(DviTRecord &$current_obj, $attribute_name, $value)
    {
        $methods =  get_class_methods(get_class($current_obj));
        $set_attibute_method = 'set_' . $attribute_name;

        if (in_array($set_attibute_method, $methods)) {
            $current_obj->$set_attibute_method($value);
            return;
        }
        $current_obj->addAttributeValue($attribute_name, $value);
    }

    protected function getFormFieldReferenceNames(): array
    {
        $groups = $this->view->getGroupFields();
        $form_field_reference_names = array();

        foreach ($groups as $group) {
            foreach ($group->getFields() as $fields) {
                foreach ($fields as $field) {
                    if (is_array($field)) {
                        $form_field_reference_names[] = $field[0];
                        continue;
                    }
                    $form_field_reference_names[] = $field;
                }
            }
        }

        return $form_field_reference_names;
    }

    protected function createCurrentObject()
    {
        if (!$this->isEditing()) {
            return;
        }

        DTransaction::open();
        $this->currentObj = $this->view->getModel()::find($this->params['id'] ?? null);
        if (!$this->currentObj) {
            new TMessage('error', 'Registro não encontrado');
            die();
        }
    }

    protected function getModelsToSave(&$models_to_save): array
    {
        $obj_master_class_name = (new \ReflectionObject($this->currentObj))->getShortName();
        $models_to_save[$obj_master_class_name] = [
            'model' => $this->currentObj,
            'class' => $this->view->getModel(),
            'parent' => null
        ];
        
        SearchActionsControl::getForeynKeys($this->currentObj, $models_to_save);

        $data = $this->view->getPanel()->getFormData();
        $result = array_merge($this->params, (array)$data);

        $models = $this->createArrayModels($result, $models_to_save);

        $new_models = array();
        foreach ($models as $key => $attributes) {
            $array = explode('.', $key);
            $model = array_pop($array);
            $new_models[] = [
                'model' => $model,
                'attributes' => $attributes
            ];
        }

        $new_models = array_reverse($new_models);

        $instance_objects = array();
        $last_model = null;
        foreach ($new_models as $key => $item) {
            if ($item['model'] == $obj_master_class_name) {
                $instance_objects[] = ['model'=>$this->currentObj, 'attributes'=> $item['attributes']];
                $last_model = $this->currentObj;
                continue;
            }
            $model = strtolower($item['model']);
            $instance_objects[$key-1]['foreing_key'] = ['name'=>$model.'_id'];

            $last_model = $last_model->$model;
            $instance_objects[] = ['model'=>$last_model, 'attributes'=> $item['attributes']];
        }
        return array_reverse($instance_objects);
    }
}
