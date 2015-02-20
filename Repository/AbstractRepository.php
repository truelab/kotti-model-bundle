<?php

namespace Truelab\KottiModelBundle\Repository;

use Doctrine\DBAL\Connection;
use Truelab\KottiModelBundle\Exception\ExpectedOneResultException;
use Truelab\KottiModelBundle\Exception\NodeByPathNotFoundException;
use Truelab\KottiModelBundle\Model\ContentInterface;
use Truelab\KottiModelBundle\Model\ModelFactory;
use Truelab\KottiModelBundle\Model\Node;
use Truelab\KottiModelBundle\Model\NodeInterface;
use Truelab\KottiModelBundle\TypeInfo\TypeInfo;
use Truelab\KottiModelBundle\TypeInfo\TypeInfoAnnotationReader;

/**
 * Class AbstractRepository
 * @package Truelab\KottiModelBundle\Repository
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    protected $typeAnnotationReader;

    protected $modelFactory;

    /**
     * @param Connection $connection
     * @param TypeInfoAnnotationReader $typeInfoAnnotationReader
     * @param ModelFactory $modelFactory
     * @param array $filters
     */
    public function __construct(Connection $connection,
                                TypeInfoAnnotationReader $typeInfoAnnotationReader,
                                ModelFactory $modelFactory, array $filters = [])
    {
        $this->connection = $connection;
        $this->typeAnnotationReader = $typeInfoAnnotationReader;
        $this->modelFactory = $modelFactory;
        $this->filters = $filters;
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
    public function findAll($class = null, array $criteria = null, array $orderBy = null, $limit = null, $offset = null)
    {

        // can return all type infos if $class == null FIXME
        $typeInfo = $this->typeAnnotationReader->inheritanceLineageTypeInfos($class);
        $nodeTypeInfo = $this->typeAnnotationReader->typeInfo(Node::getClass()); // FIXME

        if(!$class) {
            $typeInfo = $this->filter($typeInfo);
        }


        $qb = $this->connection
            ->createQueryBuilder();

        if(!$class) {
            array_unshift($typeInfo, $nodeTypeInfo);
        }

        foreach($typeInfo as $info) {
            foreach($info->getFields() as $field) {
                $qb->addSelect($field->getDottedName() . ' AS ' . $field->getAlias());
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

        if($class) {
            $preparedCriteria[] = 'nodes.type = ?';
            // node.type param
            $params[] = $this->typeAnnotationReader->typeInfo($class)->getType();
        }

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


        $collection = $this->modelFactory->createModelCollectionFromRawData(
            $this->connection->executeQuery($sql, $params)->fetchAll()
        );

        // FIXME
        foreach($collection as $node) {
            $node->setRepository($this);
        }

        return $collection;
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
     * @param TypeInfo[] $typeInfo
     *
     * @return TypeInfo[]
     */
    protected function filter(array $typeInfo)
    {
        $indexes = [];
        foreach($typeInfo as $index => $info)
        {
            foreach($this->filters as $alias) {
                if($info->getAlias() === $alias) {
                    $indexes[] = $index;
                }
            }
        }

        foreach($indexes as $i) {
            unset($typeInfo[$i]);
        }

        $typeInfo = array_values($typeInfo);

        return $typeInfo;
    }

    /**
     * @param string $class
     * @param array $criteria
     *
     * @return null|NodeInterface|ContentInterface
     * @throws \Exception
     */
    public function findOne($class = null, array $criteria = [])
    {
        $node = $this->findAll($class, $criteria);

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
     * @param string $identifier
     *
     * @return NodeInterface
     */
    public function find($class, $identifier)
    {
        return $this->findOne($class, [
            'nodes.id = ' . $identifier
        ]);
    }

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
}
