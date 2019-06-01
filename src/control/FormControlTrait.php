<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Widget\Dialog\TMessage;
use App\Http\Request;
use Dvi\Adianti\Database\Transaction;
use Dvi\Adianti\Helpers\Redirect;
use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\Model\DBFormFieldPrepare;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\Model\Relationship\HasOne;
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
    /**@var \App\Http\Request*/
    protected $request;
    /**@var FormView*/
    public $view;

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
                new TMessage('error', 'Erro ao salvar'. $e->getMessage());
            }
        }
    }

    public function onEdit(Request $request)
    {
        $this->edit($request);
    }

    public function edit(Request $request)
    {
        try {
            $request->add(['editing' => true]);

            $this->buildView($request);

            if ($this->request->has('tab')) {
                $this->view->getPanel()->setCurrentNotebookPage((int)$this->request->get('tab'));
            }

            if (editing()) {
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
        $this->buildView();

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
            $field->setValue($this->request->get($name));

            if (!$field->validate($this->request)) {
                $has_error = true;
                $field->useLabelField(true);
            }

            $this->request->result()->add([$name => $field->getValue()]);
        }

        if ($has_error) {
            $this->view->getPanel()->useLabelFields(true);
        }

        $this->getViewContent();
        if ($has_error) {
            $traits = (new ReflectionClass(self::class))->getTraitNames();
            if (in_array(ListControlTrait::class, array_values($traits))) {
                $this->loadDatagrid();
            }
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
        $last_model_short_name = Reflection::shortName($this->view->getModel());
        $last_model = $models_in_form[$last_model_short_name];
        $last_model->save();

        unset($models_in_form[$last_model_short_name]);

        //Saving associateds
        foreach ($models_in_form as $model_name => $model) {
            $associate = $last_model->getRelationship(strtolower($model_name));
            if ($associate and is_a($associate->type, HasOne::class)) {
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
        if ($this->isEditing()) {
            if (method_exists(get_called_class(), 'loadDatagrid')) {
                $this->fillDatagrid();
            }
            return;
        }

        $get_parameters = $this->request->obj()->query->all();
        $uri = '';
        collect($get_parameters)->map(function ($value, $key) use (&$uri) {
            $uri .= '/' . $key . '/' . $value;
        });

        $route = str($this->request->attr('route_base'))
            ->append('/edit/key/' . $this->currentObj->id.'/id/'.$this->currentObj->id)
            ->append($uri)->str();

        Redirect::ajaxLoadPage($route);
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
        if (!editing()) {
            return;
        }

        try {
            Transaction::open();
            $model_short_name = Reflection::shortName($this->view->getModel());
            $id_value = $this->request->get('id') ?? $this->request->get($model_short_name.'-id');

            $this->currentObj = $this->view->getModel()::find($id_value ?? null);
            if (!$this->currentObj) {
                throw new \Exception('O registro solicitado não foi encontrado.');
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage() . ' em ' . self::class . ' linha ' . $exception->getLine());
        }
    }
}
