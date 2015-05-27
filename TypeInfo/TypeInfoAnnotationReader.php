<?php

namespace Truelab\KottiModelBundle\TypeInfo;
use Doctrine\Common\Annotations\AnnotationReader;
use Truelab\KottiModelBundle\Util\Location;
use Truelab\KottiModelBundle\Model\ModelFactory;

/**
 * Class TypeInfoAnnotationReader
 * @package Truelab\KottiModelBundle\TypeInfo
 */
class TypeInfoAnnotationReader implements TypeInfoRepositoryAnnotationReaderInterface
{
    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var string
     */
    private $nodeClass;

    /**
     * @var string
     */
    private $annotationClass;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * @var array
     */
    private $typesMap;

    /**
     * @param string $annotationClass
     * @param string $nodeClass
     * @param array $typesMap
     */
    public function __construct($annotationClass, $nodeClass, $typesMap)
    {
        $this->annotationReader = new AnnotationReader();
        $this->annotationClass  = $annotationClass;
        $this->nodeClass = $nodeClass;
        $this->typesMap  = $typesMap;
    }

    /**
     * @param string $class
     *
     * @return TypeInfo|null
     */
    public function typeInfo($class) {

        if(isset($this->cache[$class])) {
            return $this->cache[$class];
        }

        try{
            $reflectionClass = new \ReflectionClass($class);
        }catch (\ReflectionException $e) {
            return null;
        }

        $annotation = $this->annotationReader->getClassAnnotation(
            $reflectionClass,
            $this->annotationClass
        );

        if(!$annotation) {
            return null;
        }

        $this->cache[$class] = $annotation;
        $this->cache[$class]->setClass($class);

        return $this->cache[$class];
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

        if(is_array($class)) {
            return $this->inheritanceLineageTypeInfosMultipleClasses($class);
        }

        $lineage = $this->inheritanceLineage($class);
        $typeInfos = [];

        foreach($lineage as $class) {
            if($typeInfo = $this->typeInfo($class)) {
                $typeInfos[] = $typeInfo;
            }
        }

        return $typeInfos;
    }


    /**
     * @param array $classes
     *
     * @return array
     */
    protected function inheritanceLineageTypeInfosMultipleClasses(array $classes)
    {
        if(empty($classes)) {
            return [];
        }

        $array = [];

        foreach($classes as $class) {

            $lineage = $this->inheritanceLineage($class);
            array_shift($lineage);

            foreach($lineage as $lineageClass) {
                if(!in_array($lineageClass, $array)) {
                    array_unshift($array, $lineageClass);
                }
            }
        }

        foreach($classes as $class) {
            array_unshift($array, $class);
        }

        $typeInfos = [];
        foreach($array as $class) {
            if($typeInfo = $this->typeInfo($class)) {
                $typeInfos[] = $typeInfo;
            }
        };

        return $typeInfos;
    }


    /**
     * @param string|string[] $aliases
     *
     * @return string|string[]
     * @throws \Exception
     */
    public function getClassByAlias($aliases)
    {

        if(is_array($aliases)) {
            return $this->getClassesByAliases($aliases);
        }

        /**
         * @var TypeInfo $typeInfo
         */
        foreach($this->cache as $class => $typeInfo)
        {
            if($typeInfo->getAlias() === $aliases) {
                return $class;
            }
        }

        $allInfo = $this->allTypeInfos();

        /**
         * @var TypeInfo $typeInfo
         */
        foreach($allInfo as $typeInfo)
        {
            if($typeInfo->getAlias() === $aliases)
            {
                return $typeInfo->getClass();
            }
        }

        throw new \Exception(sprintf('Class for requested aliases (%s) was not found!! Maybe is not registered under truelab_kotti_model.types map?', $aliases));
    }

    /**
     * Returns all the inheritance lineage for the class ( parent classes + class )
     * !!! filtering out the abstract classes
     *
     * @param string $class
     *
     * @return string[]
     */
    public function inheritanceLineage($class)
    {
        return array_values(array_filter(Location::inheritanceLineage($class), function ($class) {
            return !(new \ReflectionClass($class))->isAbstract();
        }));
    }

    /**
     * @return array
     */
    protected function allTypeInfos()
    {
        $info = [];
        foreach($this->typesMap as $type => $class) {
            $info[] = $this->typeInfo($class);
        }
        return $info;
    }

    /**
     * @param string[] $aliases
     * @return string[]
     * @throws \Exception
     */
    protected function getClassesByAliases(array $aliases)
    {
        if(empty($aliases)) {
            return [];
        }

        $classes = [];

        // search
        $allInfo = $this->allTypeInfos();

        /**
         * @var TypeInfo $typeInfo
         */
        foreach($allInfo as $typeInfo)
        {
            if(in_array($typeInfo->getAlias(), $aliases)) {
                $classes[$typeInfo->getAlias()] = $typeInfo->getClass();
            }
        }

        foreach($aliases as $alias) {
            if(!isset($classes[$alias])) {
                throw new \Exception(sprintf('Class for requested alias (%s) was not found!! Maybe is not registered under truelab_kotti_model.types map?', $alias));
            }
        }

        $asStrings = [];
        foreach($classes as $alias => $class) {
            $asStrings[] = $class;
        }
        return $asStrings;
    }

    /**
     * @param $class
     *
     * @return array|TypeInfo[]
     */
    protected function inheritanceTypeInfos($class)
    {
        if(!$class === $this->nodeClass) {
            return $this->allTypeInfos();
        }

        return $this->inheritanceLineageTypeInfos($class);
    }
}
