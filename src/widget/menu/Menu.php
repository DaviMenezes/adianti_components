<?php

namespace Dvi\Component\Menu;

use App\Config\MenuStructure;
use eftec\bladeone\BladeOne;

/**
 *  Menu
 *
 * @package
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 */
class Menu
{
    public static function getHtml()
    {
        $data = self::getStructure();

        $blade = new BladeOne(VIEW_PATH, VIEW_CACHE_PATH, BLADE_MODE);

        $html = $blade->run('menu.vertical', $data);

        return $html;
    }

    public static function getStructure()
    {
        $items = MenuStructure::items();

        foreach ($items as $key => &$item) {
            $nivel = 2;

            $item['nivel'] = $nivel;

            if (isset($item['items'])) {
                foreach ($item['items'] as &$item) {
                    self::getNewItems($nivel, $item);
                }
            }
        }
        return ['items' => $items];
    }

    protected static function getNewItems(int &$nivel, &$item)
    {
        if (isset($item['items'])) {
            $nivel ++;
            $item['nivel'] = $nivel;
            foreach ($item['items'] as &$item) {
                self::getNewItems($nivel, $item);
            }
        }
    }
}
