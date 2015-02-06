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

        //
        foreach($typeInfos as $typeInfo) {
            foreach($typeInfo->getFields() as $field) {
                $qb->addSelect($field->getDottedName() . ' AS ' . $field->getAlias());
            }
        }

        if($class) {

            // Inheritance type infos remove node
            $typeInfos = array_reverse($typeInfos, true);
            array_shift($typeInfos);

        }


        $sql = $qb->getSQL();
        $sql .= ' ' . $nodeTypeInfo->getTable();

        /**
         * @var $typeInfo TypeInfo
         */
        foreach($typeInfos as $typeInfo) {
            $sql .= ' JOIN ' . $typeInfo->getTable() . ' ON ' . $typeInfo->getAssociation();
        }

        if($class) {
            $sql .= ' WHERE nodes.type = "' .$this->_typeAnnotationReader->typeInfo($class)->getType() . '"';
        }

        // CRITERIA
        if(!$class && $criteria) {
            $sql .= ' AND ';
        }

        if($criteria) {
            foreach($criteria as $index => $c) {
                $sql .= ( $class || $index > 0 ? ' AND ' : '') . $c;
            }
        }

        return $this->_modelFactory->createModelCollection(
            $this->_connection->executeQuery($sql)->fetchAll()
        );
    }

    /**
     * @param string $class
     * @param array $criteria
     *
     * @return null|NodeInterface
     * @throws \Exception
     */
    public function findOne($class, array $criteria = [])
    {
        // TODO: Implement findOne() method.
        $node = $this->findAll($class, $criteria);

        if(empty($node)) {
           return null;
        }else if(is_array($node) && count($node) !== 1){
            throw new \Exception('Expected one result but got more.');
        }else{
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
        return $this->findOne($class, array(
            'nodes.id = ' . $identifier
        ));
    }

}
