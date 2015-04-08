<?php

namespace Truelab\KottiModelBundle\Repository\Criteria\Exception;

/**
 * Class InvalidDefaultCriteriaNameException
 * @package Truelab\KottiModelBundle\Repository\Criteria\Exception
 */
class InvalidDefaultCriteriaNameException extends \Exception
{
    /**
     * @param string $class
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($class = '', $code = 0, \Exception $previous = null)
    {
        $message = sprintf('"getName" method for "%s" must return a not empty string!', $class);
        parent::__construct($message, $code, $previous);
    }
}
