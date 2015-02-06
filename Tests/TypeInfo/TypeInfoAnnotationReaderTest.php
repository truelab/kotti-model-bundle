<?php

namespace Truelab\KottiModelBundle\Tests\TypeInfo;
use Truelab\KottiModelBundle\Model\ModelFactory;
use Truelab\KottiModelBundle\TypeInfo\TypeInfoAnnotationReader;

/**
 * Class TypeInfoAnnotationReaderTest
 * @package Truelab\KottiModelBundle\Tests\TypeInfo
 */
class TypeInfoAnnotationReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TypeInfoAnnotationReader
     */
    protected $typeInfoAnnotationReader;

    public function setUp()
    {
        $this->typeInfoAnnotationReader = new TypeInfoAnnotationReader();
    }

    public function testInheritanceLineageTypeInfosWithNoClass()
    {
        $all = $this->typeInfoAnnotationReader->inheritanceLineageTypeInfos(null);

        $this->assertCount(count(ModelFactory::$map), $all);
    }
}
