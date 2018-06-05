<?php
namespace Dvi\Adianti\Widget\Util;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Base\TElement;
use Adianti\Base\Lib\Widget\Form\AdiantiWidgetInterface;
use Adianti\Base\Lib\Widget\Util\TImage;
use Adianti\Base\Lib\Widget\Util\TTextDisplay;
use Dvi\Adianti\Control\DAction;

/**
 * Model DActionLink
 *
 * @version    Dvi 1.0
 * @package    util
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DActionLink extends TTextDisplay
{
    private $label;
    /**@var TElement $a_content*/
    private $a_content;
    protected $image_icon;
    protected $image;
    /**@var TAction $action*/
    private $action;

    public function __construct(
        DAction $action = null,
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

    public function setParameters($params)
    {
        $this->action->setParameters($params);
    }

    public function icon($icon, $style = null)
    {
        $this->image_icon['icon'] = $icon;
        $this->image_icon['style'] = $style;

        $this->image =  new TImage($icon);
        $this->image->{'style'} = $style ?? '';

        $rrpos = strrpos($icon, 'fa-');
        $class = null;
        if ($rrpos !== false) {
            $has_size = strrpos(substr($icon, $rrpos), 'x');
            if ($has_size) {
                $class = 'class = \'align_btn_content\'';
            }
        }
        $this->a_content->class = 'align_btn_content';
        $this->a_content->add('<span '.$class.'>'.$this->image.'</span>');

        return $this;
    }

    public function label($label)
    {
        $this->label = $label;

        return $this;
    }

    public function action($action, array $params = null)
    {
        if (is_array($action) or is_a($action, DAction::class)) {
            if (is_array($action)) {
                if (count($action)) {
                    $action = new DAction($action, $params);
                }
            }

            $href = $action->serialize();
            $this->{'href'} = str_replace('index', 'engine', $href);
            $this->{'generator'} = 'adianti';
        }

        return $this;
    }

    public function show()
    {
        if (!empty($this->label)) {
            $this->a_content->add('<span>'.$this->label.'</span>');
        }

        $this->action($this->action);

        parent::show();
    }
}
