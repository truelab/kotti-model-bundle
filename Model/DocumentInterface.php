<?php

namespace Truelab\KottiModelBundle\Model;

use Truelab\KottiModelBundle\TypeInfo\TypeInfo;


interface DocumentInterface
{
    public function getMimeType();

    public function getBody();
}
