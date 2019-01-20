<?php

namespace Dvi\Adianti\Helpers;

use ReflectionClass;

/**
 * Helpers Reflection
 *
 * @package    Helpers
 * @subpackage Dvi Components
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait Reflection
{
    /**
     * @param null $class
     * @return string
     */
    public static function shortName($class = null)
    {
        return self::obj($class ?? get_called_class())->getShortName();
    }

    public static function objClassName($obj)
    {
        return (new \ReflectionObject($obj))->getName();
    }

    public static function lowerName($class = null)
    {
        return strtolower(self::shortName($class));
    }

    public static function obj($class = null): ReflectionClass
    {
        return new ReflectionClass($class ?? get_called_class());
    }

    public static function getPublicPropertyNames($obj)
    {
        $rf_properties = (new \ReflectionObject($obj))->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($rf_properties as $var) {
            $prop = $var->name;
            $obj->$prop = $prop;
        }
        return $obj;
    }
}
