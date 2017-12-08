<?php
namespace Dvi\Adianti\Control;

use Adianti\Core\AdiantiCoreApplication;
use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TLabel;
use Dvi\Adianti\Database\DTransaction;
use Dvi\Adianti\Widget\Form\DEntry;
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

    public function __construct()
    {
    }

    public function createPanelForm($param)
    {
        $id = new THidden('id');

        $class = explode('\\', get_called_class());
        $name = array_pop($class);

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

            $obj = $this->objectClass::get($data->id);

            $obj->fromArray((array)$data);
            $obj->store();

            $param['id'] = $obj->id;
            $this->setFormWithParams($param);

            DTransaction::close();

            $new_params = DviControl::getNewParams($param);

            $new_params['id'] = $obj->id;

//            $this->reloadIfClassExtendFormAndListing($param);
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
                $obj = new $this->objectClass($param['id']);
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
