<?php

namespace Dvi\Adianti\View\Standard\Form;

use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Dvi\Adianti\Database\Transaction;
use Dvi\Adianti\View\Standard\DviBaseView;
use Dvi\Adianti\Widget\Container\VBox;

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

    abstract public function createPanelFields();
}
