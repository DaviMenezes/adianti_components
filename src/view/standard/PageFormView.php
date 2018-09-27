<?php
namespace Dvi\Adianti\View\Standard;

use Dvi\Adianti\Database\Transaction;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\Widget\Base\GridColumn;
use Dvi\Adianti\Widget\Form\Field\FormField;
use Dvi\Adianti\Widget\Form\PanelGroup\PanelGroup;

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
trait PageFormView
{
    protected $panel_rows_columns = array();
    /**@var PanelGroup $panel*/
    protected $panel;
    protected $content_after_panel;
    protected $build_group_fields = array();
    protected $already_create_panel_rows;

    public function buildFields()
    {
        try {
            Transaction::open();

            if (count($this->build_group_fields)) {
                return $this->build_group_fields;
            }

            $this->buildGroupFields();

            Transaction::close();

            return $this->build_group_fields;
        } catch (\Exception $e) {
            Transaction::rollback();
            throw new \Exception('Construção de campos.'.$e->getMessage());
        }
    }

    public function createPanelFields()
    {
        if ($this->already_create_panel_rows) {
            return true;
        }
        if ($this->isEditing()) {
            $this->panel->useLabelFields(true);
        }

        $rows = 0;
        $fields = $this->buildFields();
        foreach ($fields as $key => $groups) {
            if ($groups['tab'] and $key == 0) {
                $this->panel->addNotebook();
            }
            if ($groups['tab']) {
                $this->panel->appendPage($groups['tab']);
            }
            foreach ($groups['fields'] as $row_fields) {
                $columns = array();
                foreach ($row_fields as $field_array) {
                    $columns[] = new GridColumn($field_array['field'], $field_array['class'], $field_array['style']);
                }
                $this->panel->addRow($columns);
                $rows++;
            }
        }
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
        /**@var DviModel $model*/

        $dot_position = strpos($field_name, '.');
        if ($dot_position !== false) {
            $associateds = explode('.', $field_name);
            $field_name = array_pop($associateds);

            $last_associated = null;
            foreach ($associateds as $key => $associated) {
                $last_associated = $model->getForeignKeys()[$associated];
                if ($key+1 == count($associateds)) {
                    $model = new $last_associated();
                    break;
                }
            }
        }
        $array_underline = explode('_', $field_name);
        foreach ($array_underline as $key => $item) {
            $array_underline[$key] = ucfirst($item);
        }
        $method = 'createField'.implode('', $array_underline);

        if (!method_exists($model, $method)) {
            throw new \Exception('O método '.$method.' precisa ser criado no modelo '. (new \ReflectionObject($model))->getShortName());
        }
        $model->$method();
        $field_data = $model->getDviField($field_name);
        return $field_data;
    }

    protected function getDviField($model, $field_name)
    {
        return $this->getFormField($model, $field_name)->getField();
    }

    public function createActionGoBack()
    {
        return $this->panel->footerLink([$this->pageList], 'fa:arrow-left fa-2x')->label('Voltar');
    }

    public function createActionClear()
    {
        return $this->panel->addActionClear();
        $this->button_clear = $this->panel->getCurrentButton();
        return $this->button_clear;
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

    private function getFormFieldBuilt($field, $dviModel): \Dvi\Adianti\Model\Fields\DBFormField
    {
        $pos = strpos($field, '.');
        $field_name = substr($field, ($pos ? $pos + 1 : 0));

        $model_alias = substr($field, 0, $pos);

        /**@var FormField $dviField */
        if ($model_alias and in_array($model_alias, array_keys($dviModel->getForeignKeys()))) {
            $model_class = $dviModel->getForeignKeys()[$model_alias];

            $form_field = $this->getFormField(new $model_class(), $field_name);
        } else {
            $form_field = $this->getFormField($dviModel, $field_name);
        }
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

                    $build_fields[$row][] = ['field' => $dviField, 'class' => $class, 'style' => $this->getFieldStyle($component_name)];
                }
                $row++;
            }

            $this->build_group_fields[$key] = ['tab' => $group->getTab(), 'fields' => $build_fields];
        }
    }
}
