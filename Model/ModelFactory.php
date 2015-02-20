<?php

namespace Truelab\KottiModelBundle\Model;

use Truelab\KottiModelBundle\TypeInfo\TypeInfoAnnotationReader;

/**
 * Class ModelFactory
 * @package Truelab\KottiModelBundle\Model
 */
class ModelFactory
{
    private $annotationReader;

    private $typeColumn;

    private $typesMap;

    public function __construct(TypeInfoAnnotationReader $typeInfoAnnotationReader, $typeColumn, $typesMap)
    {
        $this->annotationReader = $typeInfoAnnotationReader;
        $this->typeColumn = $typeColumn;
        $this->typesMap = $typesMap;
    }

    /**
     * @param array $record
     *
     * @return NodeInterface|null
     */
    public function createModelFromRawData(array $record)
    {
        $type = $record[$this->typeColumn];

        if(!isset($this->typesMap[$type])) {
            // FIXME we must throw new \Exception(sprintf('Unknown type "%s"!', $type));
            //throw new \Exception(sprintf('Unknown type "%s"!', $type));
            return null;
        }

        $class = $this->typesMap[$type];

        $info = $this->annotationReader->inheritanceLineageTypeInfos($class);

        $object = new $class();

        foreach($record as $alias => $value)
        {
            foreach($info as $typeInfo) {
                if($field = $typeInfo->getFieldByAlias($alias)) {
                    $object[$field->getProperty()] = $value;
                }
            }
        }
        return $object;
    }

    /**
     * @param array $records
     *
     * @return NodeInterface[]
     */
    public function createModelCollectionFromRawData(array $records)
    {
        $collection = [];
        foreach($records as $record) {
            if($model = $this->createModelFromRawData($record)) {
                $collection[] = $model;
            }
        }
        return $collection;
    }

}
