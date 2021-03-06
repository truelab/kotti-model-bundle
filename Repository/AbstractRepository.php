<?php

namespace Truelab\KottiModelBundle\Repository;

use Doctrine\DBAL\Connection;
use Truelab\KottiModelBundle\Exception\ExpectedOneResultException;
use Truelab\KottiModelBundle\Exception\NodeByPathNotFoundException;
use Truelab\KottiModelBundle\Model\ContentInterface;
use Truelab\KottiModelBundle\Model\ModelFactory;
use Truelab\KottiModelBundle\Model\Node;
use Truelab\KottiModelBundle\Model\NodeInterface;
use Truelab\KottiModelBundle\Repository\Criteria\DefaultCriteriaManagerInterface;
use Truelab\KottiModelBundle\TypeInfo\TypeInfo;
use Truelab\KottiModelBundle\TypeInfo\TypeInfoAnnotationReader;
use Truelab\KottiModelBundle\TypeInfo\TypeInfoField;

/**
 * Class AbstractRepository
 * @package Truelab\KottiModelBundle\Repository
 */
abstract class AbstractRepository implements RepositoryInterface
{
    const SQLITE_PLATFORM_NAME  = 'sqlite';

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var TypeInfoAnnotationReader
     */
    protected $typeAnnotationReader;

    /**
     * @var ModelFactory
     */
    protected $modelFactory;

    /**
     * @var DefaultCriteriaManagerInterface
     */
    protected $defaultCriteriaManager;

    /**
     * @param Connection $connection
     * @param TypeInfoAnnotationReader $typeInfoAnnotationReader
     * @param ModelFactory $modelFactory
     */
    public function __construct(Connection $connection,
                                TypeInfoAnnotationReader $typeInfoAnnotationReader,
                                ModelFactory $modelFactory)
    {
        $this->connection = $connection;
        $this->typeAnnotationReader = $typeInfoAnnotationReader;
        $this->modelFactory = $modelFactory;
    }

    /**
     * @param $class
     * @param array $criteria
     * @param array $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return NodeInterface[]
     */
    public function findAll($class = null, array $criteria = null, array $orderBy = null, $limit = null, $offset = null, array $fields = [], $count = false)
    {

        $data   = $this->getFindAllSql($class, $criteria, $orderBy, $limit, $offset, $fields, $count);
        $sql    = $data['sql'];
        $params = $data['params'];
        $lazyFields = $data['lazy_fields'];

        // PREPARE STATEMENT
        $statement = $this->connection->prepare($sql);

        foreach($params as $index => $param)
        {
            $type = null;

            if($param instanceof \DateTime) {

                $type = 'datetime';

            }elseif(is_int($param)) {

                $type = \PDO::PARAM_INT;
            }

            $statement->bindValue($index + 1, $param, $type);
        }
        $statement->execute();


        // COUNT
        if($count) {
            $result = $statement->fetchAll();
            if(count($result) > 0) {
                return (int)$result[0]['total'];
            }else{
                return 0;
            }
        }

        $collection = $this->modelFactory->createModelCollectionFromRawData(
            $statement->fetchAll()
        );


        // FIXME
        foreach($collection as $node) {
            $node->setRepository($this);

            if(count($lazyFields) > 0) {

                /**
                 * @var TypeInfoField $lazyField
                 */
                foreach($lazyFields as $lazyField) {

                    $modelFactory = $this->modelFactory;
                    $connection = $this->connection;
                    $data = $this->getFindSql(get_class($node), $node['id'], [$lazyField->getName()]);
                    $sql  = $data['sql'];
                    $params = $data['params'];

                    $reference = function () use ($modelFactory, $connection, $sql, $params, $lazyField) {
                        return $modelFactory->getProperty($lazyField, $connection->executeQuery($sql, $params)->fetchAll());
                    };

                    $setReferenceMethodName = 'set'. ucfirst($lazyField->getProperty()) . 'Reference';

                    if(method_exists($node, $setReferenceMethodName))
                    {
                        $node->{$setReferenceMethodName}($reference);
                    }
                }
            }
        }

        return $collection;
    }

    /**
     * @param null $class
     * @param array $criteria
     * @param array $orderBy
     * @param null $limit
     * @param null $offset
     * @param array $fields
     * @param bool $count
     *
     * @return array
     */
    protected function getFindAllSql(
        $class = null,
        array $criteria = null,
        array $orderBy = null,
        $limit = null,
        $offset = null,
        array $fields = [],
        $count = false)
    {

        $platformName  = $this->connection->getDatabasePlatform()->getName();

        // can return all type infos if $class == null FIXME
        $typeInfo = $this->typeAnnotationReader->inheritanceLineageTypeInfos($class);
        $nodeTypeInfo = $this->typeAnnotationReader->typeInfo(Node::getClass()); // FIXME
        $lazyFields = [];


        $qb = $this->connection
            ->createQueryBuilder();

        if(!$class) {
            array_unshift($typeInfo, $nodeTypeInfo);
        }

        if($count === true) {
            $qb->addSelect('COUNT(*) AS total');
        }else{
            foreach($typeInfo as $info) {
                foreach($info->getFields() as $field) {
                    if(!$field->isLazy() || in_array($field->getName(), $fields)) {
                        $qb->addSelect($field->getDottedName() . ' AS ' . $field->getAlias());
                    }else{
                        $lazyFields[] = $field;
                    }
                }
            }
        }


        $sql = $qb->getSQL();
        $sql .= $nodeTypeInfo->getTable();


        // ------ JOIN

        // remove node before join sql parts
        if($class) {
            $typeInfo = array_reverse($typeInfo, true);
            array_shift($typeInfo);
        }else{
            array_shift($typeInfo);
        }

        /**
         * @var $info TypeInfo
         */
        foreach($typeInfo as $info) {
            $sql .= ( $class ? ' JOIN ' : ' LEFT JOIN ') . $info->getTable() . ' ON ' . $info->getAssociation();
        }


        // ------- WHERE
        $params = [];
        $preparedCriteria = [];

        // -------- restrict by type if class is set
        if($class) {
            $preparedCriteria[] = 'nodes.type = ?';
            // node.type param
            $params[] = $this->typeAnnotationReader->typeInfo($class)->getType();
        }

        // DEFAULT CRITERIA
        if($this->defaultCriteriaManager) {
            $defaultCriteria = $this->defaultCriteriaManager->process();

            if(count($defaultCriteria) > 0) {
                foreach($defaultCriteria as $key => $c) {
                    $preparedCriteria[] = $this->prepareCriteria($key, $c, $params);
                }
            }
        }

        // CRITERIA ARGUMENT
        if($criteria) {
            foreach($criteria as $key => $c) {
                $preparedCriteria[] = $this->prepareCriteria($key, $c, $params);
            }
        }

        // sanitize criteria
        foreach($preparedCriteria as &$cc)
        {
            $cc = str_replace('WHERE', '', $cc);
            $cc = trim($cc);
        }

        if(!empty($preparedCriteria)) {
            $whereSql = ' WHERE ' . (implode(' AND ', $preparedCriteria));
            $sql .= $whereSql;
        }

        // ORDER BY
        if($orderBy && !empty($orderBy)) {
            $sql .= ' ORDER BY '. (implode(',', $orderBy));
        }

        // LIMIT
        if(is_numeric($limit)) {
            if($offset == null || !is_numeric($offset)) {
                $offset = 0;
            }


            if($platformName === self::SQLITE_PLATFORM_NAME) {

                $params[] = (int) $limit;
                $params[] = (int) $offset;
                $sql .= ' LIMIT ? OFFSET ?';

            }else{

                $params[] = (int) $offset;
                $params[] = (int) $limit;
                $sql .= ' LIMIT  ?, ?';
            }
        }


        return [
            'sql' => $sql,
            'params' => $params,
            'lazy_fields' => $lazyFields
        ];
    }

    /**
     * @param null $class
     * @param $identifier
     * @param array $fields
     *
     * @return array
     */
    protected function getFindSql($class = null, $identifier, array $fields = [])
    {
        return $this->getFindAllSql($class,[
            'nodes.id = ?' => $identifier
        ], [], null, null, $fields);
    }

    protected function prepareCriteria($key, $value, &$params)
    {
        // 'contents.id = 3'
        if (is_int($key) && is_string($value)) {
            return $value;
        }

        // 'contents.id = ? ' => $id
        if (is_string($key)) {

            // 'nodes.type = ? OR contents.in_navigation = ?' => ['document', true ]
            if (is_array($value)) {

                foreach($value as $val) {
                    $params[] = $val;
                }

            } else {
                $params[] = $value;
            }

            return $key;
        }

        // [ 'contents.id' => $id ]
        if (is_array($value)) {

            if(is_array($value[key($value)])) {
                foreach($value[key($value)] as $val) {
                    $params[] = $val;
                }
            }else{
                $params[] = $value[key($value)];
            }

            return key($value);
        }
    }

    /**
     * @param string $class
     * @param array $criteria
     *
     * @param array $fields
     *
     * @return null|ContentInterface|NodeInterface
     * @throws ExpectedOneResultException
     */
    public function findOne($class = null, array $criteria = [], array $fields = [])
    {
        $node = $this->findAll($class, $criteria, $fields);

        if(empty($node)) {

            return null;

        }else if(is_array($node) && count($node) !== 1){

            throw new ExpectedOneResultException();

        }else{
            // TODO check and throws unexpected type exception if class/type?
            return $node[0];
        }
    }

    /**
     * @param string $class
     * @param string $identifier
     *
     * @param array $fields
     *
     * @return NodeInterface
     * @throws ExpectedOneResultException
     */
    public function find($class, $identifier, array $fields = [])
    {
        return $this->findOne($class, [
            'nodes.id = ' . $identifier
        ], $fields);
    }

    /**
     * @param string $path
     *
     * @return null|ContentInterface|NodeInterface
     * @throws NodeByPathNotFoundException
     */
    public function findByPath($path)
    {
        try{
            $node = $this->findOne(null, [
                [ 'WHERE nodes.path = ?' => $path ]
            ]);
        }catch(\Exception $e) {
            throw new NodeByPathNotFoundException($path, $e);
        }

        if(!$node) {
            throw new NodeByPathNotFoundException($path);
        }

        return $node;
    }

    /**
     * @param null $class
     * @param array $criteria
     * @param array $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return int
     */
    public function countAll($class = null, array $criteria = null, array $orderBy = null, $limit = null, $offset = null)
    {
       return $this->findAll($class, $criteria, $orderBy, $limit, $offset, [], true);
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createQueryBuilder()
    {
        return $this->connection->createQueryBuilder();
    }

    /**
     * @param DefaultCriteriaManagerInterface $defaultCriteriaManager
     */
    public function setDefaultCriteriaManager(DefaultCriteriaManagerInterface $defaultCriteriaManager)
    {
        $this->defaultCriteriaManager = $defaultCriteriaManager;
    }
}
