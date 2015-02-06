<?php

namespace Truelab\KottiModelBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Truelab\KottiModelBundle\Model\File;
use Truelab\KottiModelBundle\Model\LanguageRoot;
use Truelab\KottiModelBundle\Repository\Repository;
use Truelab\KottiModelBundle\Model\Document;

/**
 * Class Test
 * @package Truelab\KottiModelBundle\Tests\Repository
 */
class RepositoryFunctionalTest extends WebTestCase
{
    private $client;

    /**
     * @var Repository
     */
    private $repository;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->repository = $this->client->getContainer()->get('truelab_kotti_model.repository');
    }

    public function testFindAllNoClassAndCriteria()
    {
        $nodes = $this->repository->findAll(null);
        $this->assertGreaterThan(1, count($nodes));
    }

    public function testFindAllWithType()
    {
        $nodes = $this->repository->findAll(Document::getClass());

        $this->assertTrue(is_array($nodes), 'I expect result is array');
        $this->assertGreaterThan(1, count($nodes));
        $this->assertInstanceOf(Document::getClass(), $nodes[0]);
    }

    public function testFindAllWithTypeFile()
    {
        $nodes = $this->repository->findAll(File::getClass());
        $this->assertTrue(is_array($nodes));
    }

    public function testFindOneWithType()
    {
        $path = '/en/mip/';

        /**
         * @var Document $document
         */
        $document = $this->repository->findOne(Document::getClass(), array(
            'nodes.path = "' . $path . '"'
        ));

        $this->assertInstanceOf(Document::getClass(), $document);
        $this->assertEquals($document->getPath(), $path);
        $this->assertInstanceOf('\DateTime', $document->getCreationDate());
        $this->assertTrue(is_array($document->getAcl()), 'I expect acl is an array');
        $this->assertJson(json_encode($document));
        $this->assertArrayHasKey('path', json_decode(json_encode($document), true));
    }

    public function testFindOneByCriteria()
    {
        $path = '/en/';

        /**
         * @var Document $document
         */
        $document = $this->repository->findOne(null, [
            ['WHERE nodes.path = ?' => $path]
        ]);

        $this->assertInstanceOf(LanguageRoot::getClass(), $document);
    }

    public function testFindByPath()
    {
        $path = '/en/mip/';

        /**
         * @var Document $document
         */
        $document = $this->repository->findByPath($path);
        $this->assertInstanceOf(Document::getClass(), $document);

        $this->assertTrue($document->isInNavigation());
    }

    public function testGetChildren()
    {
        $path = '/en/';

        /**
         * @var Document $document
         */
        $document = $this->repository->findByPath($path);
        $children = $document['children'];

        $this->assertTrue(is_array($children), 'I expect children is an array');
    }
}
