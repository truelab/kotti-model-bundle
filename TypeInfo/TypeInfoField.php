<?php

namespace Truelab\KottiModelBundle\TypeInfo;
use Doctrine\Common\Util\Inflector;

/**
 * Class TypeInfoField
 * @package Truelab\KottiModelBundle\TypeInfo
 */
class TypeInfoField
{
    private $name;
    private $alias;
    private $dottedName;
    private $lazy = false;

    public function __construct($table, $field)
    {
        if(is_array($field) && !isset($field['name']))
        {
            throw new \Exception('If you want specify options for this field, you must set a "name" property');
        }

        $this->name  = is_array($field) ? $field['name'] : $field;
        $this->alias = $table . '_' . $this->name;
        $this->dottedName = $table . '.' . $this->name;

        if(is_array($field) && isset($field['lazy']) && $field['lazy'] == true) {
            $this->lazy = true;
        }
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDottedName()
    {
        return $this->dottedName;
    }

    public function getProperty()
    {
        return Inflector::camelize($this->getName());
    }

    public function isLazy()
    {
        return $this->lazy;
    }
}
