<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Registry\TSession;
use Adianti\Base\Lib\Widget\Form\TDate;
use Adianti\Base\Lib\Widget\Form\TDateTime;
use Adianti\Base\Lib\Widget\Form\TField;
use Dvi\Adianti\Database\Transaction;
use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\Model\QueryFilter;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\SearchableField;

/**
 * Control SearchListControlTrait
 *
 * @package    Control
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait SearchListControlTrait
{
    public function onSearch()
    {
        try {
            if (!$this->validateToken()) {
                throw new \Exception('Ação não permitida');
            }
            Transaction::open();

            $this->buildView($this->request);

            $this->view->getPanel()->keepFormLoaded();

            $array_models = $this->getModelAndAttributesOfTheForm();

            $filters = array();

            /**@var DviModel $model */
            foreach ($array_models as $model_name => $model) {
                $this->createFilters($model->getPublicProperties(), $model_name, $filters);
            }

            $called_class = Reflection::shortName(get_called_class());
            TSession::setValue($called_class . '_form_data', $this->view->getPanel()->getFormData());
            if (count($filters)) {
                $session_filters = TSession::getValue($called_class . '_filters');
                foreach ($filters as $key => $filter) {
                    $session_filters[$key] = $filter;
                }

                TSession::setValue($called_class . '_filters', $session_filters);
            }

            $this->loadDatagrid();

            $this->getViewContent();

            Transaction::close();
        } catch (\Exception $e) {
            Transaction::rollback();
            throw $e;
        }
    }

    protected function createFilters($attributes, $model_name, &$filters)
    {
        foreach ($attributes as $attribute => $value) {
            if (empty($value)) {
                continue;
            }

            $attribute_name = $model_name . '-' . $attribute;

            $field = $this->view->getPanel()->getForm()->getField($attribute_name);

            if (!$field) {
                continue;
            }
            /**@var TField $field */
            $field->setValue($value);

            $traits = class_uses($field);

            if (in_array(SearchableField::class, $traits)) {
                /**@var FormField $field */
                $searchOperator = $field->getSearchOperator();

                if (!is_a($field, TDate::class) and !is_a($field, TDateTime::class)) {
                    $value = $field->getSearchableValue();
                }
                $filter = new QueryFilter($model_name . '.' . $attribute, $searchOperator, $value);

                $filters[$model_name . '.' . $attribute] = $filter;
            }
        }
    }
}
