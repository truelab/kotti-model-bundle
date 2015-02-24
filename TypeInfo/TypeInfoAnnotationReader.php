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
    private $annotationReader;

    private $nodeClass;

    private $annotationClass;

    private $cache = [];

    private $typesMap;

    public function __construct($annotationClass, $nodeClass, $typesMap)
    {
        $this->annotationReader = new AnnotationReader();

        $this->annotationClass  = $annotationClass;
        $this->nodeClass = $nodeClass;
        $this->typesMap  = $typesMap;
    }

    protected function inheritanceTypeInfos($class)
    {
        if(!$class === $this->nodeClass) {
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

        if(isset($this->cache[$class])) {
            return $this->cache[$class];
        }

        $this->cache[$class] = $this->annotationReader->getClassAnnotation(
            new \ReflectionClass($class),
            $this->annotationClass
        );

        $this->cache[$class]->setClass($class);

        return $this->cache[$class];
    }

    protected function allTypeInfos()
    {
        $info = [];
        foreach($this->typesMap as $type => $class) {
            $info[] = $this->typeInfo($class);
        }
        return $info;
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

    public function getClassByAlias($alias)
    {


        if(!$alias) {
            return null;
        }

        /**
         * @var TypeInfo $typeInfo
         */
        foreach($this->cache as $class => $typeInfo)
        {
            if($typeInfo->getAlias() === $alias) {
                return $class;
            }
        }


        $allInfo = $this->allTypeInfos();

        /**
         * @var TypeInfo $typeInfo
         */
        foreach($allInfo as $typeInfo)
        {
            if($typeInfo->getAlias() === $alias)
            {
                return $typeInfo->getClass();
            }
        }

        throw new \Exception(sprintf('Class for requested alias (%s) was not found!! Maybe is not registered under truelab_kotti_model.types map?', $alias));
    }
}
