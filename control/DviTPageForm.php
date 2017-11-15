<?php
namespace Dvi\Control;

use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\THidden;
use Dvi\Database\DTransaction;
use Dvi\Widget\Form\DviPanelGroup;

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

        $name = get_called_class();
        $this->panel = new DviPanelGroup($name, $this->pageTitle);
        $this->panel->addHiddenFields([$id]);
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

            $this->reloadIfClassExtendFormAndListing($param);

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

    private function reloadIfClassExtendFormAndListing($param)
    {
        $parent_class = get_parent_class(get_called_class());
        if ($parent_class == DviTPageFormList::class) {
            $this->onReload($param);
        }
    }
}
