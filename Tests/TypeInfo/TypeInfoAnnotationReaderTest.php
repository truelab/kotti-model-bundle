<?php

namespace Truelab\KottiModelBundle\Tests\TypeInfo;

use Truelab\KottiModelBundle\Model\Content;
use Truelab\KottiModelBundle\TypeInfo\TypeInfoAnnotationReader;
use Truelab\KottiModelBundle\TypeInfo\TypeInfo;

/**
 * Class TypeInfoAnnotationReaderTest
 * @package Truelab\KottiModelBundle\Tests\TypeInfo
 */
class TypeInfoAnnotationReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Truelab\KottiModelBundle\TypeInfo\TypeInfoAnnotationReader
     */
    protected $reader;

    protected $modelMockClass = 'Truelab\KottiModelBundle\Tests\TypeInfo\ContentModelMock';

    public function setUp()
    {
        $this->reader = self::createTypeInfoAnnotationReader();
    }

    public function testTypeInfo()
    {
        $this->reader = $this->createTypeInfoAnnotationReader();

        // not existent class
        $typeInfo = $this->reader->typeInfo('\NotExists\NotExistentClass');
        $this->assertEquals(null, $typeInfo);

        // class with no type info annotation
        $typeInfo = $this->reader->typeInfo('Truelab\KottiModelBundle\Tests\TypeInfo\EmptyModelMock');
        $this->assertEquals(null, $typeInfo);

        // class with type annotation
        $typeInfo = $this->reader->typeInfo($this->modelMockClass);
        $this->assertInstanceOf('Truelab\KottiModelBundle\TypeInfo\TypeInfo', $typeInfo);
    }

    public function testInheritanceLineage()
    {
        $actual = $this->reader->inheritanceLineage($this->modelMockClass);
        $this->assertCount(4, $actual);
        $this->assertEquals([
            'Truelab\KottiModelBundle\Tests\TypeInfo\ContentModelMock',
            'Truelab\KottiModelBundle\Tests\TypeInfo\BaseContentModelMock',
            'Truelab\KottiModelBundle\Model\Content',
            'Truelab\KottiModelBundle\Model\Node',
        ], $actual);
    }

    public function testInheritanceLineageTypeInfos()
    {

        $typeInfos = $this->reader->inheritanceLineageTypeInfos($this->modelMockClass);

        $this->assertCount(3, $typeInfos);
        $this->assertEquals($this->modelMockClass, $typeInfos[0]->getClass());
        $this->assertEquals('Truelab\KottiModelBundle\Model\Content', $typeInfos[1]->getClass());
        $this->assertEquals('Truelab\KottiModelBundle\Model\Node', $typeInfos[2]->getClass());
    }

    public function testInheritanceLineageTypeInfosWithMultipleClasses()
    {
        $typeInfos = $this->reader->inheritanceLineageTypeInfos([$this->modelMockClass, 'Truelab\KottiModelBundle\Model\Document']);

        $this->assertCount(4, $typeInfos);

        $classes = array_map(function (TypeInfo $typeInfo){
            return $typeInfo->getClass();
        }, $typeInfos);

        $this->assertContains($this->modelMockClass, $classes);
        $this->assertContains('Truelab\KottiModelBundle\Model\Document', $classes);
        $this->assertContains('Truelab\KottiModelBundle\Model\Content', $classes);
        $this->assertContains('Truelab\KottiModelBundle\Model\Node', $classes);

        $typeInfos = $this->reader->inheritanceLineageTypeInfos([$this->modelMockClass, 'Truelab\KottiModelBundle\Model\Document', 'Truelab\KottiModelBundle\Model\Image']);

        $this->assertCount(6, $typeInfos);

        $classes = array_map(function (TypeInfo $typeInfo){
            return $typeInfo->getClass();
        }, $typeInfos);

        $this->assertContains($this->modelMockClass, $classes);
        $this->assertContains('Truelab\KottiModelBundle\Model\Document', $classes);
        $this->assertContains('Truelab\KottiModelBundle\Model\Content', $classes);
        $this->assertContains('Truelab\KottiModelBundle\Model\Node', $classes);
        $this->assertContains('Truelab\KottiModelBundle\Model\Image', $classes);
        $this->assertContains('Truelab\KottiModelBundle\Model\File', $classes);
    }

    public function testGetClassByAlias()
    {
        $class = $this->reader->getClassByAlias('content_model_mock');
        $this->assertEquals($this->modelMockClass, $class);
    }

    public function testGetClassesByAliases()
    {
        $classes = $this->reader->getClassByAlias(['document','content_model_mock']);
        $this->assertEquals([
            'Truelab\KottiModelBundle\Model\Document',
            $this->modelMockClass
        ], $classes);
    }

    /**
     * @expectedException \Exception
     */
    public function testGetClassByAliasThrowsException()
    {
        $this->reader->getClassByAlias('not_existent');
    }

    /**
     * @expectedException \Exception
     */
    public function testGetClassesByAliasesThrowsException()
    {
        $this->reader->getClassByAlias(['not_existent', 'document']);
    }

    /**
     * @param string $annotationClass
     * @param string $nodeClass
     * @param array  $typeMap
     *
     * @return TypeInfoAnnotationReader
     */
    public static function createTypeInfoAnnotationReader(
        $annotationClass = 'Truelab\KottiModelBundle\TypeInfo\TypeInfo',
        $nodeClass = 'Truelab\KottiModelBundle\Mode\Node', array $typeMap = [
            'document' => 'Truelab\KottiModelBundle\Model\Document',
            'image' => 'Truelab\KottiModelBundle\Model\Image',
            'content_model_mock' => 'Truelab\KottiModelBundle\Tests\TypeInfo\ContentModelMock'
        ]
    )
    {
        return new TypeInfoAnnotationReader($annotationClass, $nodeClass, $typeMap);
    }

}


// DUMB MOCKS
class EmptyModelMock {}

abstract class BaseAbstractContentModelMock extends Content{}

class BaseContentModelMock extends BaseAbstractContentModelMock {}

/**
 * Class ContentModelMock
 * @package Truelab\KottiModelBundle\Tests\TypeInfo
 *
 * @TypeInfo({
 *   "table" = "content_model_mocks",
 *   "type"  = "content_model_mock",
 *   "fields" = {"foobar"},
 *   "associated_table" = "contents",
 *   "association" = "content_model_mocks.id = contents.id"
 * })
 */
class ContentModelMock extends BaseContentModelMock
{
    protected $foobar;

    /**
     * @return mixed
     */
    public function getFoobar()
    {
        return $this->foobar;
    }

    /**
     * @param mixed $foobar
     */
    public function setFoobar($foobar)
    {
        $this->foobar = $foobar;
    }
}
