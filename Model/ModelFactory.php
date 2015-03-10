<?php

namespace Truelab\KottiModelBundle\Model;

use Truelab\KottiModelBundle\TypeInfo\TypeInfoAnnotationReader;
use Truelab\KottiModelBundle\TypeInfo\TypeInfoField;
use Truelab\KottiModelBundle\Util\PostLoaderInterface;

/**
 * Class ModelFactory
 * @package Truelab\KottiModelBundle\Model
 */
class ModelFactory
{
    /**
     * @var TypeInfoAnnotationReader
     */
    private $annotationReader;

    /**
     * @var string
     */
    private $typeColumn;

    /**
     * @var array
     */
    private $typesMap;

    /**
     * @var PostLoaderInterface[]
     */
    private $postLoaders = [];

    public function __construct(TypeInfoAnnotationReader $typeInfoAnnotationReader, $typeColumn, $typesMap)
    {
        $this->annotationReader = $typeInfoAnnotationReader;
        $this->typeColumn = $typeColumn;
        $this->typesMap = $typesMap;
    }

    /**
     * @param PostLoaderInterface $postLoader
     */
    public function addPostLoader(PostLoaderInterface $postLoader)
    {
        $this->postLoaders[] = $postLoader;
    }

    /**
     * @return \Truelab\KottiModelBundle\Util\PostLoaderInterface[]
     */
    public function getPostLoaders()
    {
        return $this->postLoaders;
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
            // FIXME maybe we must throw an exception if type was not found? new \Exception(sprintf('Unknown type "%s"!', $type));
            //throw new \Exception(sprintf('Unknown type "%s"!', $type));
            return null;
        }

        $class = $this->typesMap[$type];

        $info = $this->annotationReader->inheritanceLineageTypeInfos($class);

        // instantiate object
        $object = new $class();

        // populate object
        foreach($record as $alias => $value)
        {
            foreach($info as $typeInfo) {
                if($field = $typeInfo->getFieldByAlias($alias)) {
                    $object[$field->getProperty()] = $value;
                }
            }
        }

        // runs post loaders
        foreach($this->postLoaders as $postLoader)
        {
            if($postLoader->support($object)) {
                $postLoader->onPostLoad($object);
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

    public function getProperty(TypeInfoField $field, array $rows)
    {

        if(count($rows) > 0) {
            $row = $rows[0];

            if( isset($row[$field->getAlias()]) ) {
                return $row[$field->getAlias()];
            }
        }

        return null;
    }

}
