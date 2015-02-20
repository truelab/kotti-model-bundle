<?php

namespace Truelab\KottiModelBundle\Tests\Util;
use Truelab\KottiModelBundle\Model\Node;
use Truelab\KottiModelBundle\Model\NodeInterface;
use Truelab\KottiModelBundle\Util\ModelUtil;

/**
 * Class ModelUtilTest
 * @package Truelab\KottiModelBundle\Tests\Util
 * @group unit
 */
class ModelUtilTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterByType()
    {
        $nodes = $this->provideNodes();

        $actual = ModelUtil::filterByType($nodes, '/^document/');
        $this->assertCount(1, $actual);
        $this->assertArrayHasKey(0, $actual);
        $this->assertEquals('document', $actual[0]->getType());

        $actual = ModelUtil::filterByType($nodes, '/^document|^file/');
        $this->assertCount(3, $actual);
        $this->assertArrayHasKey(0, $actual);
        $this->assertEquals('document', $actual[0]->getType());

        $actual = ModelUtil::filterByType($nodes, '^document|^file');
        $this->assertCount(3, $actual);
        $this->assertArrayHasKey(0, $actual);
        $this->assertEquals('document', $actual[0]->getType());

        $actual = ModelUtil::filterByType($nodes, '^image');
        $this->assertCount(1, $actual);
        $this->assertArrayHasKey(0, $actual);
        $this->assertEquals('image', $actual[0]->getType());
    }


    /**
     * @return NodeInterface[]
     */
    protected function provideNodes()
    {
        return array_map(function($type) {
            $node = new Node();
            $node->setType($type);
            return $node;
        }, ['document','image','file','file','collapsable_document']);
    }

}
