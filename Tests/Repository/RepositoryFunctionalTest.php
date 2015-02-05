<?php

namespace Truelab\KottiModelBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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
        $this->repository = new Repository($this->getDatabaseConnection());
    }

    public function getDatabaseConnection()
    {
        return $this->client->getContainer()->get('database_connection');
    }

    public function testFindAllWithType()
    {
        $repository = new Repository($this->getDatabaseConnection());

        $nodes = $repository->findAll(Document::getClass());

        $this->assertTrue(is_array($nodes), 'I expect result is array');
        $this->assertGreaterThan(1, count($nodes));
        $this->assertInstanceOf(Document::getClass(), $nodes[0]);
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
}
