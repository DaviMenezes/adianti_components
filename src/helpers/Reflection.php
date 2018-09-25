<?php

namespace Dvi\Adianti\Helpers;

use Exception;
use ReflectionClass;
use ReflectionException;

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
     * @throws Exception
     */
    public static function getClassName($class = null)
    {
        try {
            return (new ReflectionClass($class ?? get_called_class()))->getShortName();
        } catch (ReflectionException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function getPublicModelPropertyNames($obj)
    {
        $rf_properties = (new \ReflectionObject($obj))->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($rf_properties as $var) {
            $prop = $var->name;
            $obj->$prop = $prop;
        }
        return $obj;
    }
}
