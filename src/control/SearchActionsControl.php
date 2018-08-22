<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Registry\TSession;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Adianti\Base\Lib\Widget\Form\TDate;
use Adianti\Base\Lib\Widget\Form\TDateTime;
use Adianti\Base\Lib\Widget\Form\TField;
use Dvi\Adianti\Database\DTransaction;
use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\Helpers\Utils;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\Model\DviTFilter;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\SearchableField;
use ReflectionClass;

/**
 * Control SearchActionsControl
 *
 * @package    Control
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait SearchActionsControl
{
    public function onSearch()
    {
        try {
            if (!$this->validateToken()) {
                throw new \Exception('Ação não permitida');
            }
            DTransaction::open();

            $this->buildView($this->params);

            $this->view->getPanel()->keepFormLoaded();

            $array_models = $this->prepareArrayModels();

            DTransaction::close();

            $filters = array();

            foreach ($array_models as $model => $attributes) {
                $this->createFilters($attributes, $model, $filters);
            }

            $called_class = Reflection::getClassName(get_called_class());
            TSession::setValue($called_class.'_form_data', $this->view->getPanel()->getFormData());
            if (count($filters)) {
                $session_filters = TSession::getValue($called_class . '_filters');
                foreach ($filters as $key => $filter) {
                    $session_filters[$key] = $filter;
                }
                TSession::setValue($called_class.'_filters', $session_filters);
            }

            $this->onReload();
        } catch (\Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    protected function prepareArrayModels()
    {
        $obj_master_class_name = (new ReflectionClass($this->view->getModel()))->getShortName();

        $default_model = $this->view->getModel();
        /**@var DviModel $objMaster */
        $objMaster = new $default_model;
        $objMaster->addAttribute('id');

        $data = (array)$this->view->getPanel()->getFormData();

        $models_to_check = $this->getForeynKeys($objMaster);
        $models_to_check[$obj_master_class_name] = [
            'model' => $this->currentObj,
            'class' => $this->view->getModel(),
            'parent' => null
        ];

        $array_models = $this->createArrayModels($data, $models_to_check);
        return $array_models;
    }

    public static function getForeynKeys($current_obj, &$models_to_check = null, $last_model = null)
    {
        if ($current_obj == null) {
            return;
        }
        $models_to_check = $models_to_check ?? array();
        $foreignKeys = $current_obj->getForeignKeys();
        $last_obj = $current_obj;
        foreach ($foreignKeys as $key => $foreignKey) {
            $fkShortName = (new ReflectionClass($foreignKey))->getShortName();

            if (array_key_exists($fkShortName, $models_to_check)) {
                continue;
            }
            $parent = null;

            if ($last_model) {
                $valid_array = explode('.', $last_model);
                if ($valid_array[0] == $valid_array[1]) {
                    continue;
                }
                $parent = $last_model.'.'. $fkShortName;
            }

            $last_obj = $last_obj->$key;

            $models_to_check[$fkShortName] = [
                'model' => $model??$fkShortName,
                'class' => $foreignKey,
                'parent' => $parent
            ];

            self::getForeynKeys($last_obj, $models_to_check, $parent ?? $fkShortName);
        }
        return $models_to_check;
    }

    protected function createFilters($attributes, $model, &$filters)
    {
        foreach ($attributes as $attribute => $value) {
            if (empty($value)) {
                continue;
            }

            $model_array = explode('.', $model);
            $model_name = array_pop($model_array);
            $attribute_name = $model_name . '-' . $attribute;


            $field = $this->view->getPanel()->getForm()->getField($attribute_name);

            if (!$field) {
                continue;
            }
            /**@var TField $field*/
            $field->setValue($value);

            $traits = class_uses($field);

            if (in_array(SearchableField::class, $traits)) {
                /**@var FormField $field*/
                $searchOperator = $field->getSearchOperator();

                if (!is_a($field, TDate::class) and !is_a($field, TDateTime::class)) {
                    $value = $field->getSearchableValue();
                }
                $filter = new DviTFilter($model_name . '.' . $attribute, $searchOperator, $value);

                $filters[$model . '.' . $attribute] = $filter;
            }
        }
    }
}
