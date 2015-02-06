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

    public static $map = [ // FIXME inject from configuration
        'content'  => 'Truelab\KottiModelBundle\Model\Content',
        'document' => 'Truelab\KottiModelBundle\Model\Document',
        'file' => 'Truelab\KottiModelBundle\Model\File',
        'image' => 'Truelab\KottiModelBundle\Model\Image',
        'language_root' => 'Truelab\KottiModelBundle\Model\LanguageRoot', // KottiMultilanguageBundle
        //'courses' => 'Truelab\KottiModelBundle\Model\Course', // MipBundle
        'event' => 'Truelab\KottiModelBundle\Model\Event', // KottiCalendarBundle,
        'calendar' => 'Truelab\KottiModelBundle\Model\Calendar',
        'base_box_manager' => 'Truelab\KottiModelBundle\Model\BaseBoxManager',
        'below_content_box_manager' => 'Truelab\KottiModelBundle\Model\BelowContentBoxManager',
        'above_content_box_manager' => 'Truelab\KottiModelBundle\Model\AboveContentBoxManager',
        'left_box_manager' => 'Truelab\KottiModelBundle\Model\LeftBoxManager',
        'right_box_manager' => 'Truelab\KottiModelBundle\Model\RightBoxManager'
    ];

    public function __construct()
    {
        $this->_typeInfoAnnotationReader = new TypeInfoAnnotationReader();
    }

    public function createModel(array $record)
    {
        $type      = $record['nodes_type']; // FIXME
        $class     = self::$map[$type];
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
