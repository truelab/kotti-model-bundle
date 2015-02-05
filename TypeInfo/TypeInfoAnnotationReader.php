<?php

namespace Truelab\KottiModelBundle\TypeInfo;
use Doctrine\Common\Annotations\AnnotationReader;
use Truelab\KottiModelBundle\Util\Location;

/**
 * Class TypeInfoAnnotationReader
 * @package Truelab\KottiModelBundle\TypeInfo
 */
class TypeInfoAnnotationReader
{
    private $_annotationReader;

    public function __construct()
    {
        $this->_annotationReader = new AnnotationReader();
    }

    protected function inheritanceTypeInfos($class)
    {
        if(!$class === 'Truelab\KottiModelBundle\Model\Node') { // FIXME
            return $this->allTypeInfos();
        }

        return $this->inheritanceLineageTypeInfos($class);
    }

    /**
     * @param string $class
     *
     * @return TypeInfo
     */
    public function typeInfo($class) {
        return $this->_annotationReader->getClassAnnotation(
            new \ReflectionClass($class),
            'Truelab\KottiModelBundle\TypeInfo\TypeInfo' // FIXME
        );
    }

    protected function allTypeInfos()
    {
        return [];
    }

    /**
     * @param $class
     *
     * @return TypeInfo[]
     */
    public function inheritanceLineageTypeInfos($class)
    {
        return array_map(function ($class)  {
            return $this->typeInfo($class);
        }, $this->inheritanceLineage($class));
    }

    public function inheritanceLineage($class)
    {
        return array_filter(Location::inheritanceLineage($class), function ($class) {
            return !(new \ReflectionClass($class))->isAbstract();
        });
    }
}
