<?php

namespace Truelab\KottiModelBundle\Util;

/**
 * Class Location
 * @package Truelab\KottiModelBundle\Util
 */
class Location
{
    public static function inheritanceLineage ($object)
    {
        $class_name = is_string($object) ? $object : get_class($object);
        $parents = array_values(class_parents($class_name));
        return array_merge(array($class_name), $parents);
    }
}


