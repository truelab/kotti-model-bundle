<?php

namespace Truelab\KottiModelBundle\Repository;

use Doctrine\DBAL\Connection;
use Truelab\KottiModelBundle\Model\ModelFactory;
use Truelab\KottiModelBundle\Model\Node;
use Truelab\KottiModelBundle\Model\NodeInterface;
use Truelab\KottiModelBundle\TypeInfo\TypeInfo;
use Truelab\KottiModelBundle\TypeInfo\TypeInfoAnnotationReader;

/**
 * Class Repository
 * @package Truelab\KottiModelBundle\Repository\Repository
 */
class Repository implements RepositoryInterface
{
    /**
     * @var Connection
     */
    private $_connection;

    private $_typeAnnotationReader;


    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->_connection = $connection;
        $this->_typeAnnotationReader = new TypeInfoAnnotationReader();
        $this->_modelFactory = new ModelFactory();
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
        $nodeTypeInfo = $this->_typeAnnotationReader->typeInfo(Node::getClass());

        // TODO cache this infos!!! injected map?
        // can return all type infos if $class == null
        $typeInfos = $this->_typeAnnotationReader->inheritanceLineageTypeInfos($class);


        $qb = $this->_connection
            ->createQueryBuilder();

        if(!$class) {
            array_unshift($typeInfos, $nodeTypeInfo);
        }

        foreach($typeInfos as $typeInfo) {
            foreach($typeInfo->getFields() as $field) {
                $qb->addSelect($field->getDottedName() . ' AS ' . $field->getAlias());
            }
        }


        // remove node before join sql parts
        if($class) {
            $typeInfos = array_reverse($typeInfos, true);
            array_shift($typeInfos);
        }else{
            array_shift($typeInfos);
        }


        $sql = $qb->getSQL();
        $sql .= ' ' . $nodeTypeInfo->getTable();

        /**
         * @var $typeInfo TypeInfo
         */
        foreach($typeInfos as $typeInfo) {
            $sql .= ( $class ? ' JOIN ' : ' LEFT JOIN ') . $typeInfo->getTable() . ' ON ' . $typeInfo->getAssociation();
        }

        $params = [];
        if($class) {
            $sql .= ' WHERE nodes.type = ? ';
            // node.type param
            $params[] = $this->_typeAnnotationReader->typeInfo($class)->getType();
        }


        if($criteria) {
            $index = 0;
            foreach($criteria as $key => $c) {
                if(!$class) {
                    if($index === 0) {
                        $sql .= ' ' . $this->prepareCriteria($c, $params);
                    }else{
                        $sql .= ' AND ' . $this->prepareCriteria($c, $params);
                    }

                }else{
                    $sql .= ( $class || $key > 0 ? ' AND ' : '') . $this->prepareCriteria($c, $params);
                }
                $index++;
            }
        }

        return $this->_modelFactory->createModelCollection(
            $this->_connection->executeQuery($sql, $params)->fetchAll()
        );
    }

    protected function prepareCriteria($criteria, &$params)
    {
        if(is_string($criteria)) {
            return $criteria;
        }

        if(is_array($criteria)) {
            $params[] = $criteria[key($criteria)];
            return key($criteria);
        }
    }

    /**
     * @param string $class
     * @param array $criteria
     *
     * @return null|NodeInterface
     * @throws \Exception
     */
    public function findOne($class = null, array $criteria = [])
    {
        $node = $this->findAll($class, $criteria);

        if(empty($node)) {

           return null;

        }else if(is_array($node) && count($node) !== 1){

            throw new \Exception('Expected one result but got more.');

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
        return $this->findOne(null, [
            [ 'WHERE nodes.path = ?' => $path ]
        ]);
    }
}
