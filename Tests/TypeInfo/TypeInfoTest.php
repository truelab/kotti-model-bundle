<?php

namespace Truelab\KottiModelBundle\Tests\TypeInfo;
use Truelab\KottiModelBundle\TypeInfo\TypeInfo;

/**
 * Class TypeInfoTest
 * @package Truelab\KottiModelBundle\Tests\TypeInfo
 * @group unit
 */
class TypeInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TypeInfo
     */
    protected $typeInfo;

    public function setUp()
    {
        $this->typeInfo = new TypeInfo(array(
            'value' => array(
                'table' => 'foos',
                'type'   => 'foo',
                'alias'  => 'foo',
                'fields' => array('body','mime_type'),
                'associated_table' => 'bars',
                'association' => 'foos.id = bars.id'
            )
        ));

        $this->typeInfo->setClass('Foo\Bar');
    }

    public function testGetFieldAlias()
    {
        $this->assertInstanceOf('Truelab\KottiModelBundle\TypeInfo\TypeInfoField', $this->typeInfo->getFieldByAlias('foos_id'));
    }

    public function testGetAlias()
    {
        $this->assertEquals('foo', $this->typeInfo->getAlias());
    }

    public function testGetClass()
    {
        $this->assertEquals('Foo\Bar', $this->typeInfo->getClass());
    }

}
