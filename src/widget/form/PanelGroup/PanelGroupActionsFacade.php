<?php

namespace Dvi\Adianti\Widget\Form\PanelGroup;

use Adianti\Base\Lib\Registry\TSession;
use Adianti\Base\Lib\Widget\Base\TElement;
use App\Http\Request;
use Dvi\Adianti\Widget\Bootstrap\Component\ButtonGroup;
use Dvi\Adianti\Widget\Form\Button;
use Dvi\Adianti\Widget\Util\Action;
use Dvi\Adianti\Widget\Util\ActionLink;

/**
 * Widget PanelGroupActionsFacade
 *
 * @package    Widget
 * @subpackage Dvi Adianti Components
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait PanelGroupActionsFacade
{
    /**@var Button*/
    protected $action_save;
    /**@var ActionLink*/
    protected $action_clear;
    /**@var Button*/
    protected $action_search;
    /**@var Button*/
    protected $action_delete;

    /**@var ButtonGroup */
    protected $group_actions;

    public function addActionSave($route, array $parameters = null, $tip = null)
    {
        $this->action_save = $this->footerButton($route, $parameters, $tip)->icon('fa:floppy-o fa-2x')->setLabel(_t('Save'));

        return $this;
    }

    public function addActionClear($route, array $parameters = null, $tip = null)
    {
        if (empty($parameters['static'])) {
            $parameters['static'] = 1;
        }
        $this->action_clear = $this->footerLink($route)
            ->icon('fa:eraser fa-2x')
            ->label(_t('Clear'))
            ->params($parameters);

        $this->action_clear->{'title'} = $tip;
        return $this;
    }

    public function addActionSearch($route = null, array $parameters = null, $tip = null)
    {
        $route = $route ?? urlRoute(Request::instance()->attr('route_base').'/search');
        $this->action_search = $this->footerButton($route, $parameters, $tip)->icon('fa:search fa-2x')->setLabel(_t('Search'));

        return $this;
    }

    public function addActionDelete($route, array $parameters = null, $tip = null)
    {
        $this->action_delete = $this->footerLink($route)->params($parameters)->icon('fa:trash  red fa-2x')->label(_t('Delete'));

        return $this;
    }

    public function footerButton(string $route, array $parameters = null, $tip = null): Button
    {
        $action = $this->group_actions->addButton($route, 'fa:floppy-o fa-2x', null, $parameters);
        $action->setTip($tip);
        return $action;
    }

    public function footerLink(string $route, string $image = null, $btn_style = 'default'): ActionLink
    {
        return $this->group_actions->addLink($route, $image)->styleBtn('btn btn-' . $btn_style . ' dvi_panel_action');
    }

    public function addActionBackLink($action = null)
    {
        $link = new ActionLink($action, _t('Back'), 'fa:arrow-left fa-2x');
        $link->class = 'btn btn-default';

        $this->hboxButtonsFooter->addButton($link);

        return $link;
    }
    //Todo verificar, se nao for usado, remover
    private function createButton($params): Button
    {
        $btn = new Button($params['id']);
        $btn->setAction(new Action($params['route'], 'GET', $params['parameters']));

        if (isset($params['label']) and $params['label']) {
            $element_label = new TElement('span');
            $element_label->add($params['label']);
            $btn->setLabel($element_label);
        } else {
            $btn->setLabel($params['label']);
        }

        $btn->class = 'btn btn-default dvi_panel_action';
        $btn->style = 'font-size: 14px;';

        return $btn;
    }

//Todo remove    public function getCurrentButton(): Button
//    {
//        return $this->currentButton;
//    }
    //Todo check if unnused, then remove
    private function createButtonLink($route, $label, $image, $class = null, $parameters = null): ActionLink
    {
        $btn = new ActionLink(new Action($route, 'GET', $parameters), $label, $image);
        $btn->class = 'dvi_panel_action ' . $class;
        return $btn;
    }

    public function getActionSave()
    {
        return $this->action_save;
    }

    public function getActionSearch()
    {
        return $this->action_search;
    }

    public function getActionClear()
    {
        return $this->action_clear;
    }

    public function getActionDelete()
    {
        return $this->action_delete;
    }
}
