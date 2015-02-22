<?php

namespace Truelab\KottiModelBundle\Tests\Model;

/**
 * Class ContentTest
 * @package Truelab\KottiModelBundle\Tests\Model
 */
class ContentTest extends AbstractKottiModelTestCase
{
    public function __construct()
    {
        $datetime = new \DateTime();
        $datetimeString = $datetime->format(\DateTime::ISO8601);

        parent::__construct('Content', [
            'type' => 'content',
            'defaultView' => 'hello',
            'description' => 'foo',
            'owner' => 'johdoe',
            'inNavigation' => true,
            'language' => 'en',
            'modificationDate' => [
                'value' => $datetimeString,
                'expected' => $datetime
            ],
            'creationDate' => [
                'value' => $datetimeString,
                'expected' => $datetime
            ],
            'state' => 'public'
        ]);
    }

    public function testIsPublic()
    {
        $this->assertTrue($this->object->isPublic());
        $this->assertFalse($this->object->isPrivate());
    }

    public function testIsPrivate()
    {
        $this->object->setState('private');
        $this->assertTrue($this->object->isPrivate());
        $this->assertFalse($this->object->isPublic());
    }
}
