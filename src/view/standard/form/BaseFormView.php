<?php

namespace Dvi\Adianti\View\Standard\Form;

use Adianti\Base\Lib\Registry\TSession;
use Dvi\Adianti\Database\Transaction;
use Dvi\Adianti\Helpers\GUID;
use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\View\Standard\DviBaseView;
use Dvi\Adianti\Widget\Form\Field\Hidden;
use Dvi\Adianti\Widget\Form\PanelGroup\PanelGroup;

/**
 * Form BaseFormView
 *
 * @package    Form
 * @subpackage Standard
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class BaseFormView extends DviBaseView
{
    /**@var PanelGroup $panel */
    protected $panel;
    protected $panel_created;

    public function createPanel($param)
    {
        try {
            if ($this->panel_created) {
                return;
            }
            Transaction::open();

            $this->createPanelForm();

            $this->createFormToken($param);

            $this->buildFields();

            $this->createPanelFields();

            $this->panel_created = true;

            Transaction::close();
        } catch (\Exception $e) {
            Transaction::rollback();
            throw new \Exception('Criação do painel.'.$e->getMessage());
        }
    }
    public function createPanelForm()
    {
        $this->panel = $this->panel ?? new PanelGroup($this->request['class']);
        $this->setPageTitle();
    }

    public function createFormToken($param)
    {
        if ($this->panel->getForm()->getField($param['class'] . '_form_token')) {
            return;
        }
        $field_id = new Hidden(Reflection::shortName($this->model) . '-id');
        $field_id->setValue($this->request['id'] ?? null);
        $field_token = new Hidden($param['class'] . '_form_token');

        $token = $param[$param['class'] . '_form_token'] ?? null;
        if (empty($param[$param['class'] . '_form_token'])) {
            $token = GUID::getID();
            TSession::setValue($param['class'] . '_form_token', $token);
        }
        $field_token->setValue($token);

        $this->panel->addHiddenFields([$field_id, $field_token]);
    }

    public function getPanel()
    {
        return $this->panel;
    }

    abstract public function createPanelFields();
}
