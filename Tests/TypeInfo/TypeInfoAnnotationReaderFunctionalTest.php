<?php

namespace Truelab\KottiModelBundle\Tests\TypeInfo;
use Truelab\KottiModelBundle\Model\ModelFactory;
use Truelab\KottiModelBundle\TypeInfo\TypeInfoAnnotationReader;

/**
 * Class TypeInfoAnnotationReaderTest
 * @package Truelab\KottiModelBundle\Tests\TypeInfo
 */
class TypeInfoAnnotationReaderFunctionalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TypeInfoAnnotationReader
     */
    protected $typeInfoAnnotationReader;

    public function setUp()
    {
        $this->typeInfoAnnotationReader = new TypeInfoAnnotationReader(
            'Truelab\KottiModelBundle\TypeInfo\TypeInfo',
            'Truelab\KottiModelBundle\Model\Node'
        );
    }

    public function testInheritanceLineageTypeInfosWithNoClass()
    {
        $all = $this->typeInfoAnnotationReader->inheritanceLineageTypeInfos(null);

        $this->assertCount(count(ModelFactory::$map), $all);
    }

    public function testGetClassByAlias()
    {
        $class = $this->typeInfoAnnotationReader->getClassByAlias('document');
        $this->assertEquals('Truelab\KottiModelBundle\Model\Document', $class);
    }
}
