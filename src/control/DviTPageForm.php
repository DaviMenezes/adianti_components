<?php
namespace Dvi\Adianti\Control;

use Adianti\Base\Lib\Core\AdiantiCoreApplication;
use Adianti\Base\Lib\Database\TTransaction;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Adianti\Base\Lib\Widget\Form\THidden;
use Dvi\Adianti\Database\DTransaction;
use Dvi\Adianti\Route;
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

    public function createPanelForm($param)
    {
        $id = new THidden('id');

        $name = Route::getClassName(get_called_class());

        $this->panel = new DviPanelGroup($name, $this->pageTitle);
        $this->panel->addHiddenFields([$id]);

        $obj = new $this->objectClass();
        $rows_form = $obj->getFormRows();

        foreach ($rows_form as $rows) {
            $this->panel->addRow($rows);
        }
    }

    public function onSave($param)
    {
        try {
            DTransaction::open($this->database);

            $this->panel->getForm()->validate();

            $data = $this->panel->getFormData();

            $obj = new $this->objectClass();
            $obj->build();

            $obj->fromArray((array)$data);

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
                $obj = $this->objectClass::find($param['id'] ?? null);
                $obj = !$obj ? new \stdClass() : $obj;
                unset($param['class']);
                unset($param['method']);
                foreach ($param as $key => $value) {
                    $obj->$key = $value;
                }
                $this->panel->setFormData($obj);
                TTransaction::close();
            } else {
                unset($param['class']);
                unset($param['method']);
                $obj = new \stdClass();
                foreach ($param as $key => $value) {
                    $obj->$key = $value;
                }
                $this->panel->setFormData($obj);
            }
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    private function reloadIfClassExtendFormAndListing($param)
    {
        $parent_class = get_parent_class(get_called_class());
        if ($parent_class == DviTPageFormList::class) {
            $this->onReload($param);
        }
    }
}
