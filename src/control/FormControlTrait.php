<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Core\AdiantiCoreApplication;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Dvi\Adianti\Database\Transaction;
use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\Helpers\Utils;
use Dvi\Adianti\Model\DBFormFieldPrepare;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\Model\Relationship;
use Dvi\Adianti\View\Standard\Form\FormView;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField as FormFieldInterface;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField as IFormField;
use Dvi\Adianti\Widget\Form\Field\FormField;
use ReflectionClass;

/**
 * Control FormControlTrait
 *
 * @package    Control
 * @subpackage Adianti
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait FormControlTrait
{
    /**@var FormView $view */
    protected $view;

    public function onSave()
    {
        try {
            Transaction::open();

            $this->beforeSave();

            $this->save();

            $this->afterSave();

            Transaction::close();
        } catch (\Exception $e) {
            Transaction::rollback();
            if ($e->getCode() == '42000' and ENVIRONMENT == 'production') {
                new TMessage('error', 'Erro ao salvar. Informe ao administrador');
            } else {
                new TMessage('error', $e->getMessage(), null, 'Erro ao salvar');
            }
        }
    }

    public function onEdit()
    {
        $this->edit();
    }

    public function edit()
    {
        try {
            $this->buildView();

            if (isset($this->request['tab']) and $this->request['tab']) {
                $this->view->getPanel()->setCurrentNotebookPage($this->request['tab']);
            }

            if ($this->isEditing()) {
                Transaction::open($this->database);

                $model_alias = Reflection::shortName($this->view->getModel());

                $query = new DBFormFieldPrepare($this->view->getModel());
                $query->mountQueryByFields($this->getFormFieldReferenceNames());
                $query->where($model_alias . '.id', '=', $this->currentObj->id);
                $result = $query->getObject();

                $formFields = $this->view->getPanel()->getForm()->getFields();
                foreach ($formFields as $formField) {
                    /**@var FormField $formField */
                    if (!method_exists($formField, 'getReferenceName')) {
                        continue;
                    }
                    $referenceName = $formField->getReferenceName();
                    if (!empty($referenceName)) {
                        $formField->setValue($result->$referenceName);
                    }
                }

                $this->getViewContent();

                Transaction::close();
            }
        } catch (\Exception $e) {
            Transaction::rollback();
            throw $e;
        }
    }

    /**@throws */
    protected function beforeSave()
    {
        if (!$this->validateToken()) {
            throw new \Exception('Ação não permitida');
        }

        $this->view->createPanelForm();
        $this->view->createFormToken($this->request);
        $this->view->buildFields();

        $fields = $this->view->getBuildFields();

        $has_error = false;
        /**@var FormFieldInterface $field */
        foreach ($fields as $field) {
            if (!in_array(IFormField::class, class_implements($field))) {
                continue;
            }
            $name = $field->getName();
            $field->setValue($this->request[$name]);

            if (!$field->validate($this->request)) {
                $has_error = true;
            }

            $value = $field->getValue();
            $this->request[$name] = $value;
        }

        if ($has_error) {
            $this->view->getPanel()->useLabelFields(true);
        }

        $this->view->createPanelFields();

        //continues bulding the view
        $this->buildView();

        $traits = (new ReflectionClass(self::class))->getTraitNames();
        if (in_array(ListControlTrait::class, array_values($traits))) {
            $this->loadDatagrid();
        }
        $this->getViewContent();
        if ($has_error) {
            throw new \Exception('Verifique os campos em destaque');
        }
    }

    protected function save()
    {
        Transaction::open();

        $this->view->getPanel()->keepFormLoaded();

        $models_in_form = $this->getModelAndAttributesOfTheForm();

        //Saving model default
        /**@var DviModel $last_model */
        $last_model = $models_in_form[Reflection::shortName($this->view->getModel())];
        $last_model->save();

        unset($models_in_form[Reflection::shortName($this->view->getModel())]);

        //Saving associateds
        foreach ($models_in_form as $model_name => $model) {
            $associate = $last_model->getRelationship(strtolower($model_name));
            if ($associate and $associate->type == Relationship::HASONE) {
                $fk = Reflection::lowerName($last_model) . '_id';
                $model->$fk = $last_model->id;
            }

            $model->save();
            $last_model = $model;
        }

        Transaction::close();
    }

    protected function afterSave()
    {
        if ($this->isEditing() and method_exists($this, 'loadDatagrid')) {
            $this->loadDatagrid();
            return;
        }

        $new_params = Utils::getNewParams();
        unset($new_params['method']);
        $new_params['id'] = $this->currentObj->id;

        $class = Reflection::shortName(get_called_class());
        AdiantiCoreApplication::loadPage($class, 'onEdit', $new_params);
    }

    protected function setFormWithParams()
    {
        $object = new \stdClass();
        foreach ($this->request as $key => $value) {
            $object->$key = $value;
        }
        $this->view->getPanel()->setFormData($object);
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

    /**Create object being edited*/
    protected function createCurrentObject()
    {
        if (!$this->isEditing()) {
            return;
        }

        try {
            Transaction::open();
            $model_short_name = Reflection::shortName($this->view->getModel());
            $id_value = $this->request['id'] ?? $this->request[$model_short_name.'-id'];
            $this->currentObj = $this->view->getModel()::find($id_value ?? null);
            if (!$this->currentObj) {
                throw new \Exception('O registro solicitado não foi encontrado.');
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage() . ' em ' . self::class . ' linha ' . $exception->getLine());
        }
    }
}
