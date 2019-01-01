<?php

namespace Dvi\Adianti\WidgetBootstrap\Component;

use Adianti\Base\Lib\Widget\Base\TElement;
use Adianti\Base\Lib\Widget\Form\TForm;
use Dvi\Adianti\Widget\Form\Button;
use Dvi\Adianti\Widget\Util\Action;
use Dvi\Adianti\Widget\Util\ActionLink;

/**
 * Component GroupActions
 *
 * @package    Component
 * @subpackage Bootstrap
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class GroupActions
{
    //Todo remove if unused protected $buttons = array();
    protected $items = array();
    protected $currentAction;
    protected $icon_size;
    /**@var TForm*/
    protected $form_default;

    public function __construct()
    {
        $this->setIconSizeDefault('fa-2x');
    }

    public function getActionIcon()
    {
        $class_icon = explode(' ', $this->action_icon);
        if (count($class_icon) > 0) {
            foreach ($class_icon as $key => $class) {
                if (count($class_icon) > 1 and $key == 0) {
                    continue;
                }

                $pos = strpos($class, 'fa');
                if ($pos === false or count($class_icon) == 1 and $this->icon_size) {
                    $this->action_icon .= ' '.$this->icon_size;
                    return $this->action_icon;
                }
            }
        }
    }

    public function setIconSizeDefault($size)
    {
        $this->icon_size = $size;
    }

    public function addButton(string $route, $icon = null, $label = null, array $parameters = null):Button
    {
        try {
            $btn = new Button();
            $btn->setAction(new Action($route, 'POST', $parameters));

            if ($label) {
                $element_label = new TElement('span');
                $element_label->add($label);

                $btn->setLabel($element_label);
            }

            if ($icon) {
                $class_icon = explode(' ', $icon);
                $pos = strpos($icon, 'fa');
                if ($pos === false or count($class_icon) == 1 and $this->icon_size) {
                    $btn->icon($icon.' '.$this->icon_size);
                } else {
                    $btn->icon($icon);
                }
            }

            $btn->class = 'btn btn-default dvi_panel_action';

            $this->buttons[] = $btn;
            $this->currentAction = $btn;
            $this->items[] = $btn;

            if ($this->form_default) {
                $this->form_default->addField($btn);
            }

            return $btn;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getButtons()
    {
        return $this->buttons;
    }

    public function addLink(string $route, $icon = null, $label = null, array $parameters = null):ActionLink
    {
        $link = new ActionLink(new Action($route, 'GET', $parameters), $label, $icon);
        $link->class = 'btn btn-default dvi_panel_action';

        $this->currentAction = $link;
        $this->items[] = $link;

        return $link;
    }

    public function getCurrentAction()
    {
        return $this->currentAction;
    }

    public function setStyle($style)
    {
        $this->currentAction->style = $style;
    }
}
