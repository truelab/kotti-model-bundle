<?php

namespace Truelab\KottiModelBundle\Tests\TypeInfo;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Truelab\KottiModelBundle\Model\ModelFactory;
use Truelab\KottiModelBundle\TypeInfo\TypeInfoAnnotationReader;

/**
 * Class TypeInfoAnnotationReaderTest
 * @package Truelab\KottiModelBundle\Tests\TypeInfo
 * @group integration
 */
class TypeInfoAnnotationReaderFunctionalTest extends WebTestCase
{
    /**
     * @var TypeInfoAnnotationReader
     */
    protected $typeInfoAnnotationReader;

    protected $client;

    protected $typesMap;

    public function setUp()
    {
        $this->client = self::createClient();
        $this->typesMap = $this->client->getContainer()->getParameter('truelab_kotti_model.types');
        $this->typeInfoAnnotationReader = new TypeInfoAnnotationReader(
            'Truelab\KottiModelBundle\TypeInfo\TypeInfo',
            'Truelab\KottiModelBundle\Model\Node',
            $this->typesMap
        );
    }

    public function testInheritanceLineageTypeInfosWithNoClass()
    {
        $all = $this->typeInfoAnnotationReader->inheritanceLineageTypeInfos(null);

        $this->assertCount(count($this->typesMap), $all);
    }

    public function testGetClassByAlias()
    {
        $class = $this->typeInfoAnnotationReader->getClassByAlias('document');
        $this->assertEquals('Truelab\KottiModelBundle\Model\Document', $class);
    }
}
