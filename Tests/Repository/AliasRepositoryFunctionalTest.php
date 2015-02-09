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
class AliasRepositoryFunctionalTest extends WebTestCase
{
    private $client;

    /**
     * @var Repository
     */
    private $repository;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->repository = $this->client->getContainer()->get('truelab_kotti_model.alias_repository');
    }


    public function testFindAllWithAlias()
    {
        $nodes = $this->repository->findAll('document');

        $this->assertTrue(is_array($nodes), 'I expect result is array');
        $this->assertGreaterThan(1, count($nodes));
        $this->assertEquals(Document::getClass(), get_class($nodes[0]));
    }
}
