<?php

namespace Truelab\KottiModelBundle\Util;
use Truelab\KottiModelBundle\Model\NodeInterface;

/**
 * Class Model
 * @package Truelab\KottiModelBundle\Util
 */
class ModelUtil
{
    const REGEX_DELIMITER = "/";
    /**
     * @param NodeInterface[] $nodes
     * @param string $pattern - a regex pattern to match against node type
     *
     * @return NodeInterface[]
     */
    public static function filterByType(array $nodes = [], $pattern)
    {
        $pattern = trim($pattern, self::REGEX_DELIMITER);

        return array_values(array_filter($nodes, function (NodeInterface $node) use ($pattern) {
            return preg_match(self::REGEX_DELIMITER . $pattern . self::REGEX_DELIMITER, $node->getType());
        }));
    }
}
