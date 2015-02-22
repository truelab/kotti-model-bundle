<?php

namespace Truelab\KottiModelBundle\Tests\Model;
use Truelab\KottiModelBundle\Model\Node;

/**
 * Class NodeTest
 * @package Truelab\KottiModelBundle\Tests\Model
 */
class NodeTest extends AbstractKottiModelTestCase
{
    /**
     * @var Node
     */
    protected $object;

    public function __construct()
    {
        parent::__construct('Node', []);
    }

    public function testGetAcl()
    {
        $this->assertInternalType('array', $this->object->getAcl());
        $this->assertInternalType('array', $this->object->getAcl());
    }

    public function testGetAnnotations()
    {
        $this->assertInternalType('array', $this->object->getAnnotations());
        $this->assertInternalType('array', $this->object->getAnnotations());
    }

    public function testEquals()
    {
        $node = $this->create();
        $this->assertTrue($this->object->equals($node));
    }


    public function testGetChildrenWithoutInjectedRepository()
    {
        $this->assertCount(0, $this->object->getChildren());
    }

    public function testGetChildren()
    {
        $repository = $this->getRepositoryMock();

        $repository
            ->expects($this->any())
            ->method('findAll')
            ->with(null,  ['nodes.parent_id = ? ' => $this->object->getId()])
            ->willReturn([
                $this->create(),
                $this->create(),
                $this->create()
            ]);

        $this->object->setRepository($repository);
        $this->assertCount(3, $this->object->getChildren());
    }

    public function testHasChildren()
    {
        $repository = $this->getRepositoryMock();
        $repository
            ->expects($this->once())
            ->method('findAll')
            ->with(null,  ['nodes.parent_id = ? ' => $this->object->getId()])
            ->willReturn([
                $this->create(),
                $this->create(),
                $this->create()
            ]);
        ;
        $this->object->setRepository($repository);

        $this->assertTrue($this->object->hasChildren());
        $this->assertTrue($this->object->hasChildren());
    }

    public function testGetInNavigationChildren()
    {
        $repository = $this->getRepositoryMock();
        $repository
            ->expects($this->once())
            ->method('findAll')
            ->with(null,  ['nodes.parent_id = ? ' => $this->object->getId(), 'contents.in_navigation = ?' => 1 ])
            ->willReturn([
                $this->create(),
                $this->create(),
                $this->create()
            ]);
        ;
        $this->object->setRepository($repository);
        $this->assertCount(3, $this->object->getInNavigationChildren());
    }

    public function testHasInNavigationChildren()
    {
        $this->assertFalse($this->object->hasInNavigationChildren());

        $repository = $this->getRepositoryMock();
        $repository
            ->expects($this->once())
            ->method('findAll')
            ->with(null,  ['nodes.parent_id = ? ' => $this->object->getId(), 'contents.in_navigation = ?' => 1 ])
            ->willReturn([
                $this->create(),
                $this->create(),
                $this->create()
            ]);
        ;
        $this->object->setRepository($repository);
        $this->assertTrue($this->object->hasInNavigationChildren());
    }

    public function testGetParent()
    {
        $this->assertNull($this->object->getParent());

        $repository = $this->getRepositoryMock();
        $repository
            ->expects($this->once())
            ->method('find')
            ->with(null,  $this->object->getParentId())
            ->willReturn($this->create());
        ;
        $this->object->setRepository($repository);

        $this->assertNotNull($this->object->getParent());
        $this->assertNotNull($this->object->getParent());
    }

    public function testHasParent()
    {
        $this->assertFalse($this->object->hasParent());

        $repository = $this->getRepositoryMock();
        $repository
            ->expects($this->once())
            ->method('find')
            ->with(null,  $this->object->getParentId())
            ->willReturn($this->create());
        ;
        $this->object->setRepository($repository);

        $this->assertTrue($this->object->hasParent());
        $this->assertTrue($this->object->hasParent());
    }

    public function testIsLeaf()
    {
        $this->assertTrue($this->object->isLeaf());
    }

    public function testIsNotLeaf()
    {
        $repository = $this->getRepositoryMock();

        $repository
            ->expects($this->any())
            ->method('findAll')
            ->with(null,  ['nodes.parent_id = ? ' => $this->object->getId()])
            ->willReturn([
                $this->create(),
                $this->create(),
                $this->create()
            ]);

        $this->object->setRepository($repository);
        $this->assertFalse($this->object->isLeaf());
    }

    public function testGetSiblings()
    {
        $repository = $this->getRepositoryMock();

        $repository
            ->expects($this->any())
            ->method('findAll')
            ->with(null,  ['nodes.parent_id = ?' => $this->object->getParentId()])
            ->willReturn([
                $this->create(),
                $this->create(),
                $this->create()
            ]);

        $this->object->setRepository($repository);
        $this->assertCount(3, $this->object->getSiblings());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getRepositoryMock()
    {
        $repository = $this
            ->getMockBuilder('Truelab\KottiModelBundle\Repository\RepositoryInterface')
            ->disableOriginalConstructor()
            ->getMock();
        return $repository;
    }
}
