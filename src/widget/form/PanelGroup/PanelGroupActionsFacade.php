<?php

namespace Dvi\Adianti\Widget\Form\PanelGroup;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Base\TElement;
use Dvi\Adianti\Helpers\GUID;
use Dvi\Adianti\Widget\Bootstrap\Component\DButtonGroup;
use Dvi\Adianti\Widget\Form\DButton;
use Dvi\Adianti\Widget\Util\DAction;
use Dvi\Adianti\Widget\Util\DActionLink;
use Dvi\Module\Office\Task\Control\TaskList2;

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
    protected $currentButton;
    /**@var DButtonGroup $group_actions*/
    protected $group_actions;

    public function addActionSave(array $parameters = null, $tip = null)
    {
        return $this->group_actions->addButton([$this->className, 'onSave'], 'fa:floppy-o fa-2x', _t('Save'), $parameters);

        $this->footerButton([$this->className, 'onSave'], $parameters, $tip)
            ->icon('fa:floppy-o fa-2x')
            ->setLabel(_t('Save'));

        return $this;
    }

    public function addActionClear(array $parameters = null, $tip = null)
    {
        return $this->footerButton([$this->className, 'onClear'], $parameters, $tip)->icon('fa:eraser fa-2x')->setLabel(_t('Clear'));

        return $this;
    }

    public function addActionSearch(array $parameters = null, $tip = null)
    {
        $this->footerButton([$this->className, 'onSearch'], $parameters, $tip)->icon('fa:search fa-2x')->setLabel(_t('Search'));

        return $this;
    }

    public function footerButton(array $callback, array $parameters = null, $tip = null)
    {
        $action = $this->group_actions->addButton($callback, 'fa:floppy-o fa-2x', null, $parameters);
        $action->setTip($tip);
        return $this->currentButton = $action;

        //Todo remove?
        $data = [
            'id' => GUID::getID(),
            'callback' => $callback,
            'parameters' => $parameters,
            'tip' => $tip
        ];
        $this->currentButton = $this->createButton($data);
        $this->hboxButtonsFooter->addButton($this->currentButton);

        $this->form->addField($this->currentButton);

        return $this->currentButton;
    }

    public function footerLink(array $callback, string $image = null, $btn_style = 'default'):DActionLink
    {
        return $this->group_actions->addLink($callback, $image)->styleBtn('btn btn-'.$btn_style.' dvi_panel_group');

        //Todo remove?
        $data = [
            'class' => 'btn btn-default',
            'callback' => $callback,
            'image' => $image,
            'label' => $label
        ];
        $this->currentButton = $this->createButtonLink($data);
        $this->hboxButtonsFooter->addButton($this->currentButton);

        return $this->currentButton;
    }

    public function addActionBackLink($action = null)
    {
        $this->currentButton = new DActionLink($action, _t('Back'), 'fa:arrow-left fa-2x');
        $this->currentButton->class = 'btn btn-default';

        $this->hboxButtonsFooter->addButton($this->currentButton);

        return $this->currentButton;
    }

    private function createButton($params):DButton
    {
        $btn = new DButton($params['id']);
        $btn->setAction(new TAction($params['callback'], $params['parameters']));

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

    public function getCurrentButton():DButton
    {
        return $this->currentButton;
    }

    private function createButtonLink($value): DActionLink
    {
        $action = new DAction($value['callback'], $value['parameters']);
        $label = $value['label'];
        $icon = $value['image'];
        $btn = new DActionLink($action, $label, $icon);
        $btn->class = 'dvi_panel_action ' . $value['class'];
        return $btn;
    }
}
