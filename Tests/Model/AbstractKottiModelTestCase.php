<?php

namespace Truelab\KottiModelBundle\Tests\Model;

/**
 * Class AbstractKottiModelTest
 * @package Truelab\KottiModelBundle\Tests\Model
 */
abstract class AbstractKottiModelTestCase extends AbstractModelTestCase
{


    public function __construct($class, $fields = array())
    {
        $class = "Truelab\\KottiModelBundle\\Model\\" . $class;
        parent::__construct($class, $fields);
    }

}
