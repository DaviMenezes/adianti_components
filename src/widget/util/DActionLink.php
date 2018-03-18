<?php
namespace Dvi\Adianti\Widget\Util;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Base\TElement;
use Adianti\Base\Lib\Widget\Util\TImage;
use Adianti\Base\Lib\Widget\Util\TTextDisplay;

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
    private $a_content;

    public function __construct(
        TAction $action = null,
        string $label = null,
        string $icon = null,
        string $color = null,
        string $size = null,
        string $decoration = null
    ) {
        $this->label = $label;

        if ($icon) {
            $this->image($icon);
        }

        parent::__construct($this->a_content, $color, $size, $decoration);
        parent::setName('a');

        $this->action($action);
    }

    public function image($icon)
    {
        $this->a_content = new TElement('span');
        $image =  new TImage($icon);
        $image->style ='float:left;';
        $this->a_content->add($image);
        if ($this->label) {
            $this->a_content->add('<div class="dvi_btn_label">'.$this->label.'</div>');
        }
        return $this;
    }

    public function action($action, array $params = null)
    {
        if (is_array($action) or is_a($action, TAction::class)) {
            if (is_array($action)) {
                if (count($action)) {
                    $action = new TAction($action, $params);
                }
            }

            $href = $action->serialize();
            $this->{'href'} = str_replace('index', 'engine', $href);
            $this->{'generator'} = 'adianti';
        }

        return $this;
    }
}
