<?php

namespace Truelab\KottiModelBundle\Tests\Model;

/**
 * Class AbstractModelTest
 * @package Truelab\KottiModelBundle\Tests\Model
 */
abstract class AbstractModelTestCase extends \PHPUnit_Framework_TestCase
{
    protected $object;
    protected $class;
    protected $fields;

    public function __construct($class, $fields = array())
    {
        $this->class = $class;
        $this->fields = array_merge([
            'id' => 1,
            'path' => '/foo/',
            'position' => 0,
            'name' => 'foo',
            'title' =>'Foo',
            'parentId' => 1,
            'annotations' => [
                'value' => '{"some_key":"some_value"}',
                'expected' => ['some_key'=>'some_value']
            ],
            'acl' => [
                'value' => '{"some_key":[{"view":"ROLE"}]}',
                'expected' => [
                    'some_key' => [
                        ['view' => 'ROLE']
                    ]
                ]
            ]
        ], $fields);
    }

    public function setUp()
    {
        $this->object = $this->create();
    }

    public function testSettersGetters()
    {
        foreach($this->fields as $field => $value)
        {
            $this->assertEquals($this->getExpected($value), $this->object[$field]);
        }
    }

    protected function create($fields = [])
    {
        $object = new $this->class();
        $fields = array_merge($this->fields, $fields);

        foreach($fields as $field => $value)
        {
            $object[$field] = $this->getValue($value);
        }
        return $object;
    }

    protected function getValue($value)
    {
        if(is_array($value)) {
            return $value['value'];
        }
        return $value;
    }

    protected function getExpected($value)
    {
        if(is_array($value)) {
            return $value['expected'];
        }
        return $value;
    }
}
