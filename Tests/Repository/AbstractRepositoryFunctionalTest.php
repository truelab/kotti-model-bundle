<?php

namespace Truelab\KottiModelBundle\Tests\Repository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AbstractRepositoryFunctionalTest
 * @package Truelab\KottiModelBundle\Tests\Repository
 */
abstract class AbstractRepositoryFunctionalTest extends WebTestCase
{
    protected function array_has_dupes($array)
    {
        // streamline per @Felix
        return count($array) !== count(array_unique($array));
    }
}
