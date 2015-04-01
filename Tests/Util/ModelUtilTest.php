<?php

namespace Truelab\KottiModelBundle\Tests\Util;
use Truelab\KottiModelBundle\Model\Content;
use Truelab\KottiModelBundle\Model\ContentInterface;
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


        $actual = ModelUtil::filterByType($nodes, '^(?!file)');
        $this->assertCount(3, $actual);
        $this->assertArrayHasKey(2, $actual);
        $this->assertEquals('collapsable_document', $actual[2]->getType());
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


    public function testFilterInNavigation()
    {
        $contents = $this->provideContents();
        $actual   = ModelUtil::filterInNavigation($contents, true);
        $this->assertCount(3, $actual);

        $contents = $this->provideContents();
        $actual   = ModelUtil::filterInNavigation($contents, false);
        $this->assertCount(2, $actual);
        $this->assertArrayHasKey(0, $actual);
        $this->assertEquals('john', $actual[0]->getOwner());

        $this->assertArrayHasKey(1, $actual);
        $this->assertEquals('ruben', $actual[1]->getOwner());
    }


    /**
     * @return ContentInterface[]
     */
    protected function provideContents()
    {
        return array_map(function ($inNavigation) {
            $content = new Content();
            $content->setInNavigation($inNavigation[0]);
            $content->setOwner($inNavigation[1]);
            return $content;
        }, [[true, 'foo'], [true, 'bar'], [false, 'john'], [true, 'doe'], [false, 'ruben']]);
    }



}
