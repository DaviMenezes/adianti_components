<?php
namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Core\AdiantiCoreApplication;
use Adianti\Base\Lib\Database\TRecord;
use Adianti\Base\Lib\Database\TTransaction;
use Adianti\Base\Lib\Registry\TSession;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Adianti\Base\Lib\Widget\Form\THidden;
use Dvi\Adianti\Database\DTransaction;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\Widget\Form\DviPanelGroup;
use Exception;

/**
 * Control DviTPageForm
 *
 * @version    Dvi 1.0
 * @package    Control
 * @subpackage component
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait DviTPageForm
{
    protected $pageTitle;

    /**@var DviPanelGroup $panel*/
    protected $panel;

    public function mountModelFields($param)
    {
        /**@var DviModel $obj*/
        $obj = new $this->objectClass();
        $rows_form = $obj->getFormRowFields();

        foreach ($rows_form as $rows) {
            $this->panel->addRow($rows);
        }
    }

    protected function beforeSave($param)
    {
        try {
            if (!parent::validateToken($param)) {
                throw new \Exception('Ação não permitida');
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            die();
        }
    }

    public function onSave($param)
    {
        $this->beforeSave($param);

        try {
            DTransaction::open($this->database);

            $this->panel->getForm()->validate();

            $data = $this->panel->getFormData();
            $result = array_merge($param, (array)$data);

            /**@var DviModel $objMaster*/
            $objMaster = new $this->objectClass($data->id ?? null);
            $objMaster->buildFieldTypes();
            $objMaster->addAttribute('id');

            $obj_master_class_name = strtolower((new \ReflectionClass($this->objectClass))->getShortName());

            $models_to_save = $objMaster->getForeignKeys();
            $models_to_save[$obj_master_class_name] = $this->objectClass;

            $array_models = $this->createArrayModels($result, $models_to_save);

            foreach ($array_models as $model => $attributes) {
                if ($obj_master_class_name !== $model) {
                    /**@var TRecord $current_obj*/
                    $current_obj = new $models_to_save[$model]();

                    $this->fillModelsWithAttributeValues($attributes, $current_obj);

                    $foreign_key_attribute = strtolower($model) . '_id';
                    $current_obj->id = $objMaster->$foreign_key_attribute;
                    $current_obj->store();

                    $objMaster->$foreign_key_attribute = $current_obj->id;
                } else {
                    $this->fillModelsWithAttributeValues($attributes, $objMaster);
                    $objMaster->store();
                }
            }

            DTransaction::close();

            $new_params = DviControl::getNewParams();
            $new_params['id'] = $objMaster->id;

            $this->afterSave($new_params);
        } catch (Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    protected function afterSave($param)
    {
        $class = (new \ReflectionClass(get_called_class()))->getShortName();
        AdiantiCoreApplication::loadPage($class, 'onEdit', $param);
    }

    protected function setFormWithParams($params)
    {
        $object = new \stdClass();
        foreach ($params as $key => $value) {
            $object->$key = $value;
        }
        $this->panel->setFormData($object);
    }

    public function onEdit($param)
    {
        try {
            if (isset($param['tab']) and $param['tab']) {
                $this->panel->setCurrentNotebookPage($param['tab']);
            }

            if (isset($param['id'])) {
                TTransaction::open($this->database);

                unset($param['class']);
                unset($param['method']);

                $form_data = new \stdClass();
                $form_data->id = $param['id'];
                $this->populateFormDataWithObjectMaster($form_data);

                $this->populateFormDataWithAssociatedObjects($form_data);

                $this->panel->setFormData($form_data);

                TTransaction::close();

                return $form_data;
            }
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    protected function createActionSave($param)
    {
        $this->panel->addActionSave();
        $this->button_save = $this->panel->getButton();
        return $this->button_save;
    }

    protected function createActionClear($param)
    {
        $this->panel->addActionClear();
        $this->button_clear = $this->panel->getButton();
        return $this->button_clear;
    }

    protected function populateFormDataWithObjectMaster(&$form_data)
    {
        $current_obj_class_name = ucfirst((new \ReflectionClass(get_class($this->currentObj)))->getShortName());
        $attribute_id = $current_obj_class_name.'_id';
        $form_data->$attribute_id = $form_data->id;

        foreach ($this->currentObj->getAttributes() as $attribute) {
            $attribute_id = $current_obj_class_name . '_' . $attribute;
            $form_data->$attribute_id = $this->currentObj->$attribute;
        }
    }

    protected function createArrayModels($result, $models_to_save): array
    {
        $array_models = array();
        foreach ($result as $atribute => $value) {
            if (!$this->isObjectAttribute($atribute)) {
                continue;
            }

            $array_attribute = explode('_', $atribute);

            $model = strtolower($array_attribute[0]);

            if (in_array($model, array_keys($models_to_save))) {
                unset($array_attribute[0]);
                $atribute = implode('_', $array_attribute);
                $array_models[$model][$atribute] = $value;
            }
        };

        return $array_models;
    }

    protected function fillModelsWithAttributeValues($attributes, TRecord &$current_obj)
    {
        $obj_attributes = $current_obj->getAttributes();

        foreach ($attributes as $attribute_name => $value) {
            if (in_array($attribute_name, array_keys($obj_attributes))) {
                $class_name = parent::getClassName($current_obj);
                $formField = $this->panel->getForm()->getField($class_name.'_'.$attribute_name);

                $method_exist = $this->getFieldClassMethods($formField);

                if ($method_exist and $formField->isDisabled()) {
                    continue;
                }
                $this->setAttributeValue($current_obj, $attribute_name, $value);
            }
        }
    }

    protected function setAttributeValue(TRecord &$current_obj, $attribute_name, $value)
    {
        $methods =  get_class_methods(get_class($current_obj));
        $set_attibute_method = 'set_' . $attribute_name;

        if (in_array($set_attibute_method, $methods)) {
            $current_obj->$set_attibute_method($value);
            return;
        }
        $current_obj->$attribute_name = $value;
    }

    protected function saveCurrentModel($model, $obj_master_class_name, $current_obj, $objMaster)
    {
        if ($model !== $obj_master_class_name) {
            $foreign_key_attribute = strtolower($model) . '_id';
            $current_obj->id = $objMaster->$foreign_key_attribute;
            $current_obj->store();

            $objMaster->$foreign_key_attribute = $current_obj->id;

            return;
        }
        $objMaster->store();
    }

    protected function isObjectAttribute($atribute)
    {
        $array_attribute = explode('_', $atribute);
        if (count($array_attribute) == 1) {
            return false;
        }
        return true;
    }

    protected function populateFormDataWithAssociatedObjects(&$form_data)
    {
        $associated_objects = $this->currentObj->getForeignKeys();
        foreach ($associated_objects as $associated) {
            $associated_model = (new \ReflectionClass($associated))->getShortName();
            $short_name = strtolower($associated_model);

            /**@var DviModel $associated_obj */
            $foreign_key_attribute_name = $short_name . '_id';
            $associated_obj = $associated::find($this->currentObj->$foreign_key_attribute_name);
            if ($associated_obj) {
                foreach ($associated_obj->getAttributes() as $attribute) {
                    $model_attribute_name = $associated_model . '_' . $attribute;
                    $form_data->$model_attribute_name = $associated_obj->$attribute;
                }
            }
        }
    }

    /**
     * @throws \ReflectionException
     */
    protected function getFieldClassMethods($formField): bool
    {
        $rf = new \ReflectionClass(get_class($formField));
        $methods = $rf->getMethods();
        $method_exist = in_array('isDisabled', $methods);
        return $method_exist;
    }

    private function reloadIfClassExtendFormAndListing($param)
    {
        $parent_class = get_parent_class(get_called_class());
        if ($parent_class == DviSearchList::class) {
            $this->onReload($param);
        }
    }

    protected function keepFormLoadedWithDataSearched()
    {
        $called_class = DControl::getClassName(get_called_class());

        $data = TSession::getValue($called_class . '_form_data');
        if (isset($data)) {
            if (!isset($this->currentObj) or
                (isset($this->currentObj) and $this->currentObj->id == $data->current_obj_id)) {
                $this->panel->setFormData($data);
            }
        }
    }
}
