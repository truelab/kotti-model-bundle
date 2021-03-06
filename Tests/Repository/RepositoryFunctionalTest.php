<?php

namespace Truelab\KottiModelBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Truelab\KottiModelBundle\Model\Document;
use Truelab\KottiModelBundle\Model\File;
use Truelab\KottiModelBundle\Repository\Repository;

/**
 * Class Test
 * @package Truelab\KottiModelBundle\Tests\Repository
 * @group functional
 */
class RepositoryFunctionalTest extends WebTestCase {
	private $client;

	/**
	 * @var Repository
	 */
	private $repository;

	public function setUp() {
		$this->client = static::createClient();
		$this->repository = $this->client->getContainer()->get('truelab_kotti_model.repository');
	}

	public function testFindAllNoClassAndCriteria() {
		$nodes = $this->repository->findAll(null);
		$this->assertGreaterThan(1, count($nodes));
	}

	public function testFindAllWithType() {
		$nodes = $this->repository->findAll(Document::getClass());

		$this->assertTrue(is_array($nodes), 'I expect result is array');
		$this->assertGreaterThan(1, count($nodes));
		$this->assertInstanceOf(Document::getClass(), $nodes[0]);
	}

	public function testFindAllWithTypeFile() {
		$nodes = $this->repository->findAll(File::getClass());
		$this->assertTrue(is_array($nodes));
	}

    public function testFindAllWithDateTimeCriteria()
    {
        $nodes = $this->repository->findAll(Document::getClass(), [
            'contents.creation_date <= ?' => new \DateTime('2015-03-21'),
        ]);

        $this->assertGreaterThan(1, count($nodes));

        // nodes can't have future creation_date (year 2094, i'm already die!)
        $nodes = $this->repository->findAll(Document::getClass(), [
            'contents.creation_date >= ?' => new \DateTime('2094-03-21'),
        ]);

        $this->assertCount(0, $nodes);
    }

    public function testFindAllWithLimit()
    {
        $nodes = $this->repository->findAll(null, [], [], 3);
        $this->assertCount(3, $nodes);
    }

	public function testFindOneWithType() {
		$path = '/about/';

		/**
		 * @var Document $document
		 */
		$document = $this->repository->findOne(Document::getClass(), array(
			'nodes.path = "' . $path . '"',
		));

		$this->assertEquals(Document::getClass(), get_class($document));
		$this->assertEquals($document->getPath(), $path);
		$this->assertInstanceOf('\DateTime', $document->getCreationDate());
		$this->assertTrue(is_array($document->getAcl()), 'I expect acl is an array');
		$this->assertJson(json_encode($document));
		$this->assertArrayHasKey('path', json_decode(json_encode($document), true));
	}

	/**
	 * @expectedException \Truelab\KottiModelBundle\Exception\ExpectedOneResultException
	 * @throws \Truelab\KottiModelBundle\Exception\ExpectedOneResultException
	 */
	public function testFindOneThrowsException() {
		$this->repository->findOne(null, [
			'nodes.path LIKE ?' => '%about%',
		]);
	}

	public function testFindByPath() {
		$path = '/about/foo/';

		/**
		 * @var Document $document
		 */
		$document = $this->repository->findByPath($path);
		$this->assertInstanceOf(Document::getClass(), $document);

		$this->assertTrue($document->isInNavigation());
	}

	public function testGetChildren() {
		$path = '/about/';

		/**
		 * @var Document $document
		 */
		$document = $this->repository->findByPath($path);

		// get children of the same type
		$children = $document
			->getChildren(get_class($document));

		foreach ($children as $child) {
			$this->assertEquals(Document::getClass(), get_class($child));
		}
	}

	public function testGetParent() {
		$parentPath = '/about/';
		$childPath = '/about/foo/';
		$document = $this->repository->findByPath($childPath);
		$this->assertEquals($parentPath, $document->getParent()->getPath());
	}

	public function testGetParent2() {
		$childPath = '/about/foo/';
		$document = $this->repository->findByPath($childPath);
		$this->assertEquals('/', $document->getParent()->getParent()->getPath());
	}

	public function testGetParent3() {
		$childPath = '/about/foo/';
		$document = $this->repository->findByPath($childPath);
		$this->assertEquals(null, $document->getParent()->getParent()->getParent());
	}

	public function testHasParent() {
		$childPath = '/about/foo/';
		$document = $this->repository->findByPath($childPath);
		$this->assertTrue($document->hasParent());

		$root = $this->repository->findByPath('/');
		$this->assertFalse($root->hasParent());
	}

    public function testCountAll()
    {
        $count = $this->repository->countAll(Document::getClass());
        $this->assertNotNull($count);
        $this->assertTrue(is_int($count), 'I expect count result is a int number');
    }

}
