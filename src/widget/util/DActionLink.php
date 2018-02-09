<?php
namespace Dvi\Adianti\Widget\Util;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Util\TActionLink;
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
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DActionLink extends TTextDisplay
{
    public function __construct(
        TAction $action,
        string $label = null,
        string $icon = null,
        string $color = null,
        string $size = null,
        string $decoration = null
    )
    {
        if ($icon) {
            $image = new TImage($icon);
            $image->style ='float:left;';
            $image .= '<div class="dvi_btn_label">'.$label.'</div>';
            ;
        }

        parent::__construct($image, $color, $size, $decoration);
        parent::setName('a');

        $href = $action->serialize();
        $this->{'href'} = str_replace('index', 'engine', $href);
        $this->{'generator'} = 'adianti';
    }
}
