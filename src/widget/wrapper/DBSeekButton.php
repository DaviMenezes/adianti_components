<?php

namespace Dvi\Adianti\Widget\Wrapper;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Core\AdiantiApplicationConfig;
use Adianti\Base\Lib\Core\AdiantiCoreTranslator;
use Adianti\Base\Lib\Database\TCriteria;
use Adianti\Base\Lib\Database\TTransaction;
use Adianti\Base\Lib\Widget\Form\TSeekButton;
use Adianti\Base\Lib\Widget\Wrapper\TDBSeekButton;
use Dvi\Adianti\Widget\Util\Action;

/**
 * Widget DBSeekButton
 *
 * @package    Widget
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 * @see https://t.me/davimenezes
 */
class DBSeekButton extends TSeekButton
{
    private $receive_key;
    private $receive_display_field;
    private $route;
    private $database;
    private $form;
    private $model;
    private $display_field;
    private $criteria;
    private $operator;

    public function __construct(string $route, string $name, string $database, string $form, string $model, string $display_field)
    {
        $this->route = $route;
        $this->name = $name;
        $this->database = $database;
        $this->form = $form;
        $this->model = $model;
        $this->display_field = $display_field;

        parent::__construct($name);
    }

    public function setCriteria(TCriteria $criteria, $operator = 'like')
    {
        $this->criteria = $criteria;
        $this->operator = $operator;
    }

    public function createAction()
    {
        if ($this->action) {
            return;
        }
        $model = str_replace('\\', '|', $this->model);
        $ini  = AdiantiApplicationConfig::get();
        $seed = APPLICATION_NAME . (!empty($ini['general']['seed']) ? $ini['general']['seed'] : 's8dkld83kf73kf094');

        // define the action parameters
        $this->action = new Action($this->route);
        $this->action->setParameter('hash', md5("{$seed}{$this->database}{$model}{$this->display_field}"));
        $this->action->setParameter('database', $this->database);
        $this->action->setParameter('parent', $this->form);
        $this->action->setParameter('model', $model);
        $this->action->setParameter('display_field', $this->display_field);
        $this->action->setParameter('receive_key', $this->receive_key ?? $this->name);
        $this->action->setParameter('receive_field', $this->receive_display_field);
        $this->action->setParameter('criteria', base64_encode(serialize($this->criteria)));
        $this->action->setParameter('operator', ($this->operator == 'ilike') ? 'ilike' : 'like');
        $this->action->setParameter('mask', '');
        $this->action->setParameter('label', AdiantiCoreTranslator::translate('Description'));
        parent::setAction($this->action);
    }

    public function setReceiveKey(string $receive_key)
    {
        $this->receive_key = $receive_key;
    }

    public function setReceiveDisplayField(string $receive_display_field)
    {
        $this->receive_display_field = $receive_display_field;
    }

    public function show()
    {
        $this->createAction();

        parent::show();
    }

    /**@return Action*/
    public function getAction()
    {
        return parent::getAction();
    }

    /**
     * Define the field's value
     * @param $value [Current value]
     */
    public function setValue($value)
    {
        parent::setValue($value);

        if (!empty($this->auxiliar)) {
            $database = $this->getAction()->getParameter('database');
            $model    = $this->getAction()->getParameter('model');
            $mask     = $this->getAction()->getParameter('mask');
            $display_field = $this->getAction()->getParameter('display_field');

            TTransaction::open($database);
            $activeRecord = new $model($value);

            if (!empty($mask)) {
                $this->auxiliar->setValue($activeRecord->render($mask));
            } elseif (isset($activeRecord->$display_field)) {
                $this->auxiliar->setValue($activeRecord->$display_field);
            }

            TTransaction::close();
        }
    }

    protected function serialized()
    {
        /**@var Action $action*/
        $action = $this->getAction();
        $url_params = '';
        collect($action->getParameters())->map(function ($value, $key) use (&$url_params) {
            $url_params .= (!empty($url_params) ? '&'.$key : $key).'='.$value;
        });

        return $url_params;
    }

    protected function createOnClick(string $serialized_action): void
    {
        $this->button->{'onclick'} = "javascript:serialform=(\$('#{$this->formName}').serialize());__dvi_adianti_append_page('".$this->route."', '{$serialized_action}/'+ serialform)";
    }
}
