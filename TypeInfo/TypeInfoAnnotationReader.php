<?php

namespace Truelab\KottiModelBundle\TypeInfo;
use Doctrine\Common\Annotations\AnnotationReader;
use Truelab\KottiModelBundle\Util\Location;
use Truelab\KottiModelBundle\Model\ModelFactory;

/**
 * Class TypeInfoAnnotationReader
 * @package Truelab\KottiModelBundle\TypeInfo
 */
class TypeInfoAnnotationReader
{
    private $_annotationReader;

    private $_nodeClass;

    private $_annotationClass;

    public function __construct($annotationClass, $nodeClass)
    {
        $this->_annotationReader = new AnnotationReader();
        $this->_annotationClass  = $annotationClass;
        $this->_nodeClass = $nodeClass;
    }

    protected function inheritanceTypeInfos($class)
    {
        if(!$class === $this->_nodeClass) {
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
            $this->_annotationClass
        );
    }

    protected function allTypeInfos()
    {
        $typeInfos = [];
        foreach(ModelFactory::$map as $type => $class) {
            $typeInfos[] = $this->typeInfo($class);
        }
        return $typeInfos;
    }

    /**
     * @param $class
     *
     * @return TypeInfo[]
     */
    public function inheritanceLineageTypeInfos($class)
    {
        if(!$class) {
            return $this->allTypeInfos();
        }

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
