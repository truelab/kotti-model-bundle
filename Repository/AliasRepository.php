<?php

namespace Truelab\KottiModelBundle\Repository;

use Doctrine\DBAL\Connection;
use Truelab\KottiModelBundle\Exception\NodeByPathNotFoundException;
use Truelab\KottiModelBundle\Model\ModelFactory;
use Truelab\KottiModelBundle\Model\Node;
use Truelab\KottiModelBundle\Model\NodeInterface;
use Truelab\KottiModelBundle\TypeInfo\TypeInfo;
use Truelab\KottiModelBundle\TypeInfo\TypeInfoAnnotationReader;

/**
 * Class Repository
 * @package Truelab\KottiModelBundle\Repository\Repository
 */
class AliasRepository extends Repository
{

    /**
     * @override
     *
     * @param string|array|null $alias
     * @param array $criteria
     * @param array $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return NodeInterface[]
     */
    public function findAll($alias = null, array $criteria = null, array $orderBy = null, $limit = null, $offset = null)
    {
        if(class_exists($alias)) {
            // alias is a existing class name
            return parent::findAll($alias, $criteria, $orderBy, $limit, $offset);
        }
        $class = $this->typeAnnotationReader->getClassByAlias($alias);
        return parent::findAll($class, $criteria, $orderBy, $limit, $offset);
    }

}
