<?php

namespace Truelab\KottiModelBundle\Exception;

/**
 * Class UnknownTypeException
 * @package Truelab\KottiModelBundle\Exception
 */
class UnknownTypeException extends \Exception
{
    public function __construct($unknownType)
    {
        $message = sprintf('Unknown type "%s"!', $unknownType);

        parent::__construct($message);
    }
}
