<?php

namespace Truelab\KottiModelBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Truelab\KottiModelBundle\Model\File;
use Truelab\KottiModelBundle\Model\LanguageRoot;
use Truelab\KottiModelBundle\Model\NodeInterface;
use Truelab\KottiModelBundle\Repository\Repository;
use Truelab\KottiModelBundle\Model\Document;

/**
 * Class Test
 * @package Truelab\KottiModelBundle\Tests\Repository
 * @group functional
 */
class AliasRepositoryFunctionalTest extends AbstractRepositoryFunctionalTest
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


    public function testFindAllWithAliases()
    {
        $nodes = $this->repository->findAll(['document','file']);

        $this->assertTrue(is_array($nodes), 'I expect result is array');
        $this->assertGreaterThan(1, count($nodes));

        foreach($nodes as $node) {
            $this->assertTrue(get_class($node) === File::getClass() || get_class($node) === Document::getClass());
        }

        $ids = array_map(function (NodeInterface $node) {
            $this->assertTrue(get_class($node) === File::getClass() || get_class($node) === Document::getClass());
            return $node->getId();
        }, $nodes);

        $this->assertFalse($this->array_has_dupes($ids));
    }

}
