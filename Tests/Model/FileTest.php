<?php

namespace Truelab\KottiModelBundle\Tests\Model;

/**
 * Class FileTest
 * @package Truelab\KottiModelBundle\Tests\Model
 */
class FileTest extends AbstractKottiModelTestCase
{
    public function __construct()
    {
        parent::__construct('File', [
            'data' => 'document',
            'filename' => 'foo.pdf',
            'mimetype' => 'pdf',
            'size' => 10000
        ]);
    }
}
