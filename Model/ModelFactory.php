<?php

namespace Truelab\KottiModelBundle\Model;
use Doctrine\Common\Annotations\AnnotationReader;
use Truelab\KottiModelBundle\TypeInfo\TypeInfo;
use Truelab\KottiModelBundle\TypeInfo\TypeInfoAnnotationReader;

/**
 * Class ModelFactory
 * @package Truelab\KottiModelBundle\Model
 */
class ModelFactory
{
    private $_typeInfoAnnotationReader;

    private $map = [ // FIXME inject from configuration
        'document' => 'Truelab\KottiModelBundle\Model\Document',
        'content'  => 'Truelab\KottiModelBundle\Model\Content'
    ];

    public function __construct()
    {
        $this->_typeInfoAnnotationReader = new TypeInfoAnnotationReader();
    }

    public function addModel($type, $class)
    {
        $this->map[$type] = $class;
    }

    public function createModel(array $record)
    {
        $type      = $record['nodes_type']; // FIXME
        $class     = $this->map[$type];
        $typeInfos = $this->_typeInfoAnnotationReader->inheritanceLineageTypeInfos($class);

        $object = new $class(); // FIXME

        foreach($record as $alias => $value)
        {
            foreach($typeInfos as $typeInfo) {
                if($field = $typeInfo->getFieldByAlias($alias)) {
                    $object[$field->getProperty()] = $value;
                }
            }
        }

        return $object;
    }

    public function createModelCollection(array $records)
    {
        $collection = [];
        foreach($records as $record) {
            $collection[] = $this->createModel($record);
        }
        return $collection;
    }

}
