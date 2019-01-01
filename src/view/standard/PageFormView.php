<?php

namespace Dvi\Adianti\View\Standard;

use App\Http\Request;
use Dvi\Adianti\Helpers\Utils;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Model\RelationshipModelType;
use Dvi\Adianti\View\Standard\Form\FormView;
use Dvi\Adianti\Widget\Base\GridColumn;
use Dvi\Adianti\Widget\Form\PanelGroup\PanelGroup;

/**
 * Control DviTPageForm
 * @package    Control
 * @subpackage component
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait PageFormView
{
    /**@var Request*/
    protected $request;
    protected $panel_rows_columns = array();
    /**@var PanelGroup */
    protected $panel;
    protected $content_after_panel;
    protected $build_group_fields = array();
    protected $already_create_panel_rows;

    public function buildFields()
    {
        try {
            if (count($this->build_group_fields)) {
                return;
            }

            $this->buildGroupFields();
        } catch (\Exception $e) {
            throw new \Exception('Construção de campos.' . $e->getMessage());
        }
    }

    public function groupFields()
    {
        return collect($this->build_group_fields);
    }

    public function createPanelFields()
    {
        if ($this->already_create_panel_rows) {
            return true;
        }
        if ($this->isEditing()) {
            $this->panel->useLabelFields(true);
        }

        $this->buildFields();

        $this->createPanelContent();

        $this->already_create_panel_rows = true;
    }

    public function alreadyCreatePanelRows()
    {
        return $this->already_create_panel_rows;
    }

    public function getBuildFields()
    {
        $build_fields = array();
        foreach ($this->build_group_fields as $group_field) {
            foreach ($group_field['fields'] as $fields) {
                foreach ($fields as $field) {
                    $build_fields[] = $field['field'];
                }
            };
        }
        return $build_fields;
    }

    protected function getFormField($model, $field_name)
    {
        $method = 'createField'. collect(explode('_', $field_name))
            ->map(function ($name) {
                return ucfirst($name);
            })->implode('');

        if (!method_exists($model, $method)) {
            throw new \Exception('O método ' . $method . ' precisa ser criado no modelo ' . (new \ReflectionObject($model))->getShortName());
        }
        $model->$method();
        $field_data = $model->getDviField($field_name);
        return $field_data;
    }

    protected function getDviField($model, $field_name)
    {
        return $this->getFormField($model, $field_name)->getField();
    }

    public function createActionGoBack($route = null)
    {
        $route = $route ?? urlRoute($this->request->attr('route_page_list'));
        return $this->panel->footerLink($route, 'fa:arrow-left fa-2x')->label('Voltar');
    }

    public function createActionClear($route = null)
    {
        $route = $route ?? urlRoute($this->request->attr('route_base').'/clear');
        $this->panel->addActionClear($route);

//Todo check        $this->panel->getActionClear()->getAction()
//            ->setParameters([
//                'form_name' => $this->panel->getForm()->getName(),
//                'form_token' => $this->panel->getForm()->getField('form_token')->getvalue(),
//                'static' => 1
//            ]);
    }

    public function createActionDelete($route = null)
    {
        if (!Utils::editing($this->request)) {
            return $this;
        }
        $id = $this->request->get('id');
        $route = $route ?? urlRoute($this->request->attr('route_base').'/delete/key/'. $id.'/id/'.$id.'/&static=1');
        $this->panel->addActionDelete($route);

        return $this;
    }

    public function createContentAfterPanel($obj = null)
    {
        $this->content_after_panel = $obj;
    }

    public function getContentAfterPanel()
    {
        return $this->content_after_panel;
    }

    private function getFieldClass($component_name)
    {
        $class = is_array($component_name) ? ($component_name[1] ?? null) : null;
        if (is_array($class)) {
            $class = implode(' ', array_values($component_name[1]));
        }
        return $class;
    }

    /**
     * @param $component_name
     * @return mixed|null
     */
    private function getFieldStyle($component_name)
    {
        $style = is_array($component_name) ? ($component_name[2] ?? null) : null;
        return $style;
    }

    private function getFormFieldBuilt($field, $dviModel): DBFormField
    {
        $pos = strpos($field, '.');
        $field_name = substr($field, ($pos ? $pos + 1 : 0));

        $model_alias = substr($field, 0, $pos);

        /**@var DviModel $dviModel */
        $relationships = $dviModel->relationships();
        if ($relationships->has($model_alias)) {
            /**@var RelationshipModelType $model_type */
            $model_type = $relationships->get($model_alias);

            $model_class = $model_type->getClassName();
            $model = new $model_class();
            $form_field = $this->getFormField($model, $field_name);
            return $form_field;
        }

        $form_field = $this->getFormField($dviModel, $field_name);

        return $form_field;
    }

    private function buildGroupFields()
    {
        /**@var DviModel $dviModel */
        $dviModel = new $this->model();

        /**@var GroupFieldView $group */
        foreach ($this->groupFields as $key => $group) {
            $build_fields = array();
            $row = 0;
            $group_fields = $group->getFields();
            foreach ($group_fields as $fields) {
                foreach ($fields as $component_name) {
                    if (!$component_name) {
                        throw new \Exception('Campo inválido. Verifique o nome dos campos.');
                    }
                    $field = is_array($component_name) ? $component_name[0] : $component_name;
                    $class = $this->getFieldClass($component_name);

                    $form_field = $this->getFormFieldBuilt($field, $dviModel);

                    if ($form_field->getHideInEdit()) {
                        continue;
                    }

                    $dviField = $form_field->getField()->setReferenceName($field);

                    if (in_array(get_parent_class($this), [FormView::class, FormListView::class])) {
                        $dviField->class .= ' dvi_field_required';
                    }

                    $build_fields[$row][] = [
                        'field' => $dviField,
                        'class' => $class,
                        'style' => $this->getFieldStyle($component_name)
                    ];
                }
                $row++;
            }

            $this->build_group_fields[$key] = collect(['tab' => $group->getTab(), 'fields' => collect($build_fields)]);
        }
    }

    protected function createPanelContent()
    {
        $this->groupFields()->map(function ($group, $key) {
            if ($group->get('tab')) {
                if ($key == 0) {
                    $this->panel->addNotebook();
                }
                $this->panel->appendPage($group->get('tab'));
            }
            $group->get('fields')->each(function ($row_fields) {
                $columns = collect($row_fields)->map(function ($field_array) {
                    return new GridColumn($field_array['field'], $field_array['class'], $field_array['style']);
                })->all();
                $this->panel->addRow($columns);
            });
        });
    }
}
