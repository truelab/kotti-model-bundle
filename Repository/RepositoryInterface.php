<?php

namespace Truelab\KottiModelBundle\Repository;


use Truelab\KottiModelBundle\Model\ContentInterface;
use Truelab\KottiModelBundle\Model\NodeInterface;

interface RepositoryInterface
{
    /**
     * @param $class
     * @param array $criteria
     * @param array $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return NodeInterface[]
     */
    public function findAll($class = null, array $criteria = null, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param string $class
     * @param array $criteria
     *
     * @return NodeInterface|ContentInterface
     */
    public function findOne($class = null, array $criteria = null);

    /**
     * @param $class
     * @param string $identifier
     *
     * @return NodeInterface
     */
    public function find($class, $identifier);

    /**
     * @param string $path
     *
     * @return NodeInterface
     */
    public function findByPath($path);

    public function countAll($class = null, array $criteria = null, array $orderBy = null, $limit = null, $offset = null);
}
