<?php

namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Database\TFilter;
use Adianti\Base\Lib\Registry\TSession;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Dvi\Adianti\Database\DTransaction;
use Dvi\Adianti\Model\DviModel;
use Dvi\Adianti\Widget\Form\DviPanelGroup;
use Dvi\Adianti\Widget\Form\Field\FormField;
use Dvi\Adianti\Widget\Form\Field\SearchableField;

/**
 * control DviTPageSearch
 *
 * @version    Dvi 1.0
 * @package    control
 * @subpackage component
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait DviTPageSearch
{
    protected $pageTitle;

    /**@var DviPanelGroup $panel*/
    protected $panel;

    public function onSearch()
    {
        try {
            if (!$this->validateToken()) {
                throw new \Exception('Ação não permitida');
            }
            DTransaction::open();

            $this->panel->keepFormLoaded();

            $obj_master_class_name = strtolower((new \ReflectionClass($this->objectClass))->getShortName());

            /**@var DviModel $objMaster*/
            $objMaster = new $this->objectClass();
            $objMaster->addAttribute('id');

            $data = (array)$this->panel->getFormData();

            $models_to_save = $objMaster->getForeignKeys();
            $models_to_save[$obj_master_class_name] = $this->objectClass;

            $array_models = $this->createArrayModels($data, $models_to_save);

            $filters = array();

            foreach ($array_models as $model => $array_model) {
                foreach ($array_model as $attribute => $value) {
                    if (empty($value)) {
                        continue;
                    }

                    $modelShortName = DControl::getClassName($models_to_save[$model]);
                    /**@var FormField $field*/
                    $field = $this->panel->getForm()->getField($modelShortName.'_'.$attribute);

                    if (!$field) {
                        continue;
                    }
                    $field->setValue($value);

                    $traits = class_uses($field);

                    if (in_array(SearchableField::class, $traits)) {
                        $searchOperator = $field->getSearchOperator();
                        $filters[] = new TFilter($attribute, $searchOperator, $field->getSearchableValue());
                    }
                }
            }

            $called_class = DControl::getClassName(get_called_class());
            TSession::setValue($called_class.'_form_data', $this->panel->getFormData());
            TSession::setValue($called_class.'_filters', $filters);

            DTransaction::close();

            $this->onReload();
        } catch (\Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    protected function createActionSearch()
    {
        $this->panel->addActionSearch();
        $this->panel->getButton()
            ->getAction()
            ->setParameters(self::getNewParams());
    }
}
