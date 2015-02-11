<?php

namespace Truelab\KottiModelBundle\Exception;

/**
 * Class ExpectedOneResultException
 * @package Truelab\KottiModelBundle\Exception
 */
class ExpectedOneResultException extends \Exception
{
    public $message = 'Expected one result but got more.';
}
