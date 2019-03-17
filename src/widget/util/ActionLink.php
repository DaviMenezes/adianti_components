<?php
namespace Dvi\Adianti\Widget\Util;

use Adianti\Base\Lib\Widget\Base\TElement;
use Adianti\Base\Lib\Widget\Util\TImage;
use Adianti\Base\Lib\Widget\Util\TTextDisplay;
use Stringy\StaticStringy;

/**
 *  ActionLink
 * @package    util
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class ActionLink extends TTextDisplay
{
    protected $label;
    /**@var TElement*/
    protected $a_content;
    protected $image_icon;
    protected $image;
    /**@var Action*/
    protected $action;
    protected $icon_size;

    public function __construct(
        Action $action = null,
        string $label = null,
        string $icon = null,
        string $color = null,
        string $size = null,
        string $decoration = null
    ) {
        $this->label = $label;
        $this->action = $action;

        $this->a_content = new TElement('span');

        if ($icon) {
            $this->icon($icon);
        }

        parent::__construct($this->a_content, $color, $size, $decoration);
        parent::setName('a');
    }

    public function getAction()
    {
        return $this->action;
    }

    public function params($params = null)
    {
        if (empty($params)) {
            return $this;
        }
        $this->action->setParameters($params);
        return $this;
    }

    public function setParameters($params = null)
    {
        if (empty($params)) {
            return $this;
        }
        $this->action->setParameters($params);
        return $this;
    }

    public function icon($icon, $style = null)
    {
        $this->image_icon['icon'] = $icon;
        if ($style) {
            $this->image_icon['style'] = $style;
        }

        $this->image =  new TImage($icon);
        if ($style) {
            $this->image->{'style'} = $style ?? '';
        }

        return $this;
    }

    public function label($label)
    {
        $this->label = $label;

        return $this;
    }

    public function action($action, array $params = null)
    {
        $parameters = '';
        if ($params) {
            foreach ($params as $key => $value) {
                if ($key == 'static') {
                    continue;
                }
                $parameters .= '/'.$key.'/';
                if (is_int($value)) {
                    $parameters .= $value;
                    continue;
                }
                $parameters .= urlencode($value);
            }
        }

        $route = $action->getAction(). $parameters;
        $params = collect($params);
        if ($params->has('static') and $params->get('static') == '1') {
            $route .= '/&static=1';
        }

        $this->{'href'} = $route;
        $this->{'generator'} = 'adianti';
        return $this;
        //Todo remove
//        if (is_array($action) or is_a($action, Action::class)) {
//            if (is_array($action)) {
//                if (count($action)) {
//                    $action = new Action($action, $params);
//                }
//            }
//
////  Todo remove          $href = $action->serialize();
//            $this->{'href'} = URLBASE.'/'.implode('/', collect($params)->filter()->all());
//            $this->{'generator'} = 'adianti';
//        }
    }

    public function styleBtn($bootstrap_class)
    {
        $this->{'class'} = $bootstrap_class;
        return $this;
    }

    public function show()
    {
        if (empty($this->image) and empty($this->label)) {
            throw new \Exception('O botão precisa de um texto ou uma imagem');
        }
//        $this->a_content->class = 'align_action_middle';

        if ($this->image) {
            $rrpos = strrpos($this->image_icon['icon'], 'fa-');
            $class = null;
            $style = null;
            if ($rrpos !== false) {
                $has_size = strrpos(substr($this->image_icon['icon'], $rrpos), 'x');
                if ($has_size) {
                    $class = 'class = "align_action_middle"';
                }
            } else {
//                $style = 'style = "vertical-align:text-bottom"';
            }
            $this->a_content->add('<span '.$class.' '.$style.'>'.$this->image.'</span>');
        }

        if (!empty($this->label)) {
            $this->a_content->add('<span class="action_label">'.$this->label.'</span>');
        }

        $this->action($this->action, $this->action->getParameters());

        parent::show();
    }
}
