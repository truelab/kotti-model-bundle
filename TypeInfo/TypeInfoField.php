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

    public function __construct($table, $field)
    {
        $this->name  = $field;
        $this->alias = $table . '_' . $this->name;
        $this->dottedName = $table . '.' . $this->name;
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
}
