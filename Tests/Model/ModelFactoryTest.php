<?php

namespace Truelab\KottiModelBundle\Tests\Model;
use Truelab\KottiModelBundle\Model\ContentInterface;
use Truelab\KottiModelBundle\Model\ModelFactory;
use Truelab\KottiModelBundle\Model\NodeInterface;
use Truelab\KottiModelBundle\TypeInfo\TypeInfo;
use Truelab\KottiModelBundle\TypeInfo\TypeInfoAnnotationReader;
use Truelab\KottiModelBundle\TypeInfo\TypeInfoField;
use Truelab\KottiModelBundle\Util\PostLoaderInterface;

/**
 * Class ModelFactoryTest
 * @package Truelab\KottiModelBundle\Tests\Model
 * @group unit
 */
class ModelFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ModelFactory
     */
    private $modelFactory;

    public function setUp()
    {
        $this->modelFactory = new ModelFactory(
            $this->getTypeInfoAnnotationReaderMock(),
            $this->getTypeColumnMock(),
            $this->getTypesMapMock()
        );
    }

    public function testAddPostLoader()
    {
        $postLoader = $this->getMock(
            'Truelab\KottiModelBundle\Util\PostLoaderInterface'
        );
        $this->modelFactory->addPostLoader($postLoader);


        $this->assertEquals($this->modelFactory->getPostLoaders()[0], $postLoader);

        $postLoader = $this->getMock(
            'Truelab\KottiModelBundle\Util\PostLoaderInterface'
        );
        $this->modelFactory->addPostLoader($postLoader);
        $this->assertCount(2, $this->modelFactory->getPostLoaders());
    }

    public function testCreateModelFromRawData()
    {
        $model = $this->modelFactory->createModelFromRawData([
           $this->getTypeColumnMock() => 'document',
           'id' => 1,
           'path' => '/foo/',
           'parentId' => 1,
           'title' => 'foo',
           'name' => 'foo',
           'annotations' => '{"colour":"red"}',
           'body' => 'bar',
           'mimeType' => 'text/html'
        ]);
        $this->assertInstanceOf($this->getTypesMapMock()['document'], $model);
        $this->assertEquals('/foo/', $model->getPath());
    }

    public function testCreateModelFromRawDataWithPostLoaders()
    {
        $this->modelFactory->addPostLoader((new PostLoaderMock('/new/path/')));
        $model = $this->modelFactory->createModelFromRawData([
            $this->getTypeColumnMock() => 'document',
            'id' => 1,
            'path' => '/foo/',
            'parentId' => 1,
            'title' => 'foo',
            'name' => 'foo',
            'annotations' => '{"colour":"red"}',
            'body' => 'bar',
            'mimeType' => 'text/html'
        ]);
        $this->assertEquals('/new/path/', $model->getPath());
    }


    protected function getTypeColumnMock()
    {
        return 'type';
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockBuilder|TypeInfoAnnotationReader
     */
    protected function getTypeInfoAnnotationReaderMock()
    {
        $mock = $this
            ->getMockBuilder('Truelab\KottiModelBundle\TypeInfo\TypeInfoAnnotationReader')
            ->disableOriginalConstructor()
            ->getMock();

        $mock
            ->expects($this->any())
            ->method('inheritanceLineageTypeInfos')
            ->willReturn([
                new TypeInfoMock(null)
            ]);

        return $mock;
    }

    public function getTypesMapMock()
    {
        return [
            'content'  => 'Truelab\KottiModelBundle\Model\Content',
            'document' => 'Truelab\KottiModelBundle\Model\Document'
        ];
    }
}


class TypeInfoMock extends TypeInfo
{
    public function getFieldByAlias($alias)
    {
        return new TypeInfoFieldMock($alias);
    }
}

class TypeInfoFieldMock extends TypeInfoField
{
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function getProperty()
    {
        return $this->alias;
    }
}


class PostLoaderMock implements PostLoaderInterface
{
    /**
     * @var string
     */
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function support($content)
    {
        return true;
    }

    public function onPostLoad($content)
    {
        if(method_exists($content, 'setPath')) {
            $content->setPath($this->path);
        }
    }
}
