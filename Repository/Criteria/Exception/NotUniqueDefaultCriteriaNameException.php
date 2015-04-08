<?php

namespace Truelab\KottiModelBundle\Repository\Criteria\Exception;

/**
 * Class NotUniqueDefaultCriteriaNameException
 * @package Truelab\KottiModelBundle\Repository\Criteria\Exception
 */
class NotUniqueDefaultCriteriaNameException extends \Exception
{
    /**
     * @param string $name
     * @param string $class
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($name = '', $class = '', $code = 0, \Exception $previous = null)
    {
        $message = sprintf('A default criteria with name "%s" already registered by class: "%s"', $name, $class);

        parent::__construct($message, $code, $previous);
    }
}
