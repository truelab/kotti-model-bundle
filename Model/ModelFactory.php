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

//    public static $map = [ // FIXME inject from configuration
//
//        // base
//        'content'  => 'Truelab\KottiModelBundle\Model\Content',
//        'document' => 'Truelab\KottiModelBundle\Model\Document',
//        'file' => 'Truelab\KottiModelBundle\Model\File',
//        'image' => 'Truelab\KottiModelBundle\Model\Image',
//
//        // kotti multilanguage
//        'language_root' => 'Truelab\KottiModelBundle\Model\LanguageRoot', // KottiMultilanguageBundle
//
//        // mip
//        'event' => 'Truelab\KottiModelBundle\Model\Event', // KottiCalendarBundle,
//        'calendar' => 'Truelab\KottiModelBundle\Model\Calendar',
//
//        // mip
//        //'courses' => 'Truelab\KottiModelBundle\Model\Course', // MipBundle
//        'base_box_manager' => 'Truelab\KottiModelBundle\Model\BaseBoxManager',
//        'below_content_box_manager' => 'Truelab\KottiModelBundle\Model\BelowContentBoxManager',
//        'above_content_box_manager' => 'Truelab\KottiModelBundle\Model\AboveContentBoxManager',
//        'left_box_manager' => 'Truelab\KottiModelBundle\Model\LeftBoxManager',
//        'right_box_manager' => 'Truelab\KottiModelBundle\Model\RightBoxManager'
//    ];

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
