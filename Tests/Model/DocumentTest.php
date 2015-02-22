<?php

namespace Truelab\KottiModelBundle\Tests\Model;

/**
 * Class DocumentTest
 * @package Truelab\KottiModelBundle\Tests\Model
 */
class DocumentTest extends AbstractKottiModelTestCase
{
    public function __construct()
    {
        parent::__construct('Document', [
            'type' => 'document',
            'body' => 'hello',
            'mimeType' => 'text/html'
        ]);
    }
}
