<?php

namespace Truelab\KottiModelBundle\Repository;


use Doctrine\DBAL\Query\QueryBuilder;
use Truelab\KottiModelBundle\Model\ContentInterface;
use Truelab\KottiModelBundle\Model\NodeInterface;

interface RepositoryInterface
{
    /**
     * @param null|string|string[] $class
     * @param null|array $criteria
     * @param null|array $orderBy
     * @param null|int $limit
     * @param null|int $offset
     *
     * @param string[] $fields - fields to exclude from select
     * @param bool $count   - count instead of fetch
     *
     * @return \Truelab\KottiModelBundle\Model\NodeInterface[]|int
     */
    public function findAll($class = null, array $criteria = null, array $orderBy = null, $limit = null, $offset = null, array $fields = [], $count = false);

    /**
     * @param null|string $class
     * @param null|array $criteria
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

    /**
     * @param null|string|string[] $class
     * @param null|array $criteria
     * @param null|array $orderBy
     * @param null|int $limit
     * @param null|int $offset
     *
     * @return int
     */
    public function countAll($class = null, array $criteria = null, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder();

}
