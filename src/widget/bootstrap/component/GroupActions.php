<?php

namespace Dvi\Adianti\WidgetBootstrap\Component;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Base\TElement;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Dvi\Adianti\Control\DAction;
use Dvi\Adianti\Widget\Form\DButton;
use Dvi\Adianti\Widget\Util\DActionLink;

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
    protected $buttons = array();
    protected $items = array();
    protected $currentAction;
    private $icon_size;

    private $label;

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

//    public function label($label)
//    {
//        $this->label = $label;
//        return $this;
//    }
    public function setIconSizeDefault($size)
    {
        $this->icon_size = $size;
    }

    public function addButton(array $action, $icon = null, $label = null, array $parameters = null, $style = null, $form_name = null)
    {
        try {
            //            if (!$form_name) {
            //                throw new \Exception('O nome do formulário é obrigatório para ações em '. array_pop(explode('\\', __METHOD__)));
            //            }
            $btn = new DButton();
            $btn->setAction(new TAction($action, $parameters));

            if ($label) {
                $element_label = new TElement('span');
                $element_label->add($label);

                /*if ($icon) {
                    $rrpos = strrpos($icon, 'fa-');
                    $has_size = strrpos(substr($icon, $rrpos), 'x');
                    if ($has_size !== false) {
                        $element_label->style = 'font-size: 14px;';
                    }
                }*/
                $btn->setLabel($element_label);
            }

            if ($icon) {
                $btn->setImage($icon);
            }

            //            $btn->setTip($value['tip']);
            $btn->class = 'btn btn-default dvi_btn';
            $btn->style = 'font-size: 14px;';

            $this->buttons[] = $btn;
            $this->currentAction = $btn;
            $this->items[] = $btn;

            return $this;
        } catch (\Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public function getButtons()
    {
        return $this->buttons;
    }

    public function addLink(array $callback, $icon = null, $label = null, array $parameters = null, $style = null):DActionLink
    {
        $link = new DActionLink(new DAction($callback, $parameters), $label, $icon);
        $link->class = 'btn btn-default';

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