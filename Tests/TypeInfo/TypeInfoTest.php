<?php

namespace Truelab\KottiModelBundle\Tests\TypeInfo;
use Truelab\KottiModelBundle\TypeInfo\TypeInfo;

/**
 * Class TypeInfoTest
 * @package Truelab\KottiModelBundle\Tests\TypeInfo
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
                'fields' => array('body','mime_type'),
                'associated_table' => 'bars',
                'association' => 'foos.id = bars.id'
            )
        ));
    }

    public function testGetAlias()
    {
        $this->assertInstanceOf('Truelab\KottiModelBundle\TypeInfo\TypeInfoField', $this->typeInfo->getFieldByAlias('foos_id'));
    }
}
