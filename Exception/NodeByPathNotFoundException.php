<?php

namespace Truelab\KottiModelBundle\Exception;


class NodeByPathNotFoundException extends \Exception
{
    const MESSAGE = 'Node with path "%s" not found!';

    /**
     * @param string     $path
     * @param \Exception $previous
     */
    public function __construct($path, \Exception $previous = null)
    {
        $message = sprintf(self::MESSAGE, $path);
        parent::__construct($message, 0, $previous);
    }
}
