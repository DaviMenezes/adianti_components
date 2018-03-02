<?php
namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Core\AdiantiCoreApplication;
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
        $id = new THidden('id');

        $this->panel->addHiddenFields([$id]);

        /**@var DviModel $obj*/
        $obj = new $this->objectClass();
        $rows_form = $obj->getFormRowFields();

        foreach ($rows_form as $rows) {
            $this->panel->addRow($rows);
        }

        $this->keepFormLoadedWithDataSearched();
    }

    public function onSave($param)
    {
        try {
            DTransaction::open($this->database);

            $this->panel->getForm()->validate();

            $data = $this->panel->getFormData();
            $result = array_merge($param, (array)$data);

            /**@var DviModel $obj*/
            $obj = new $this->objectClass($data->id ?? null);
            $obj->buildFieldTypes();

            $obj->addAttribute('id');
            $attributes = $obj->getAttributes();

            $methods = get_class_methods(get_class($obj));

            foreach ($attributes as $key => $value) {
                if (in_array($value, array_keys($result))) {
                    $set_attibute_method = 'set_'.$key;

                    if (in_array($set_attibute_method, $methods)) {
                        $obj->$key = $value;
                        continue;
                    }
                    $obj->$value = $result[$value];
                }
            }

            $obj->store();

            $param['id'] = $obj->id;
            $this->setFormWithParams($param);

            DTransaction::close();

            $new_params = DviControl::getNewParams($param);

            $new_params['id'] = $obj->id;

            AdiantiCoreApplication::loadPage(get_called_class(), 'onEdit', $new_params);

            $this->panel->keepFormLoaded();

            return $obj;
        } catch (Exception $e) {
            DTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
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
            if (isset($param['id'])) {
                TTransaction::open($this->database);

                $this->currentObj = $this->objectClass::find($param['id'] ?? null);
                $this->currentObj = !$this->currentObj ? new \stdClass() : $this->currentObj;

                unset($param['class']);
                unset($param['method']);

                foreach ($param as $key => $value) {
                    $this->currentObj->$key = $value;
                }

                $this->panel->setFormData($this->currentObj);

                TTransaction::close();
            } else {
                unset($param['class']);
                unset($param['method']);

                $this->currentObj = new \stdClass();
                foreach ($param as $key => $value) {
                    $this->currentObj->$key = $value;
                }

                $this->panel->setFormData($this->currentObj);
            }
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    protected function createActionSave()
    {
        $this->panel->addActionSave();
        $this->button_save = $this->panel->getButton();
    }

    protected function createActionClear()
    {
        $this->panel->addActionClear();
        $this->button_clear = $this->panel->getButton();
    }

    private function reloadIfClassExtendFormAndListing($param)
    {
        $parent_class = get_parent_class(get_called_class());
        if ($parent_class == DviTPageFormList::class) {
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
