<?php

namespace Truelab\KottiModelBundle\Repository;

use Truelab\KottiModelBundle\Model\NodeInterface;

/**
 * Class AliasRepository
 * @package Truelab\KottiModelBundle\Repository\AliasRepository
 */
class AliasRepository extends AbstractRepository
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
    public function findAll($alias = null, array $criteria = null, array $orderBy = null, $limit = null, $offset = null, array $fields = [])
    {

        if(class_exists($alias)) {
            // alias is a existing class name
            return parent::findAll($alias, $criteria, $orderBy, $limit, $offset, $fields);
        }

        $class = $this->typeAnnotationReader->getClassByAlias($alias);

        return parent::findAll($class, $criteria, $orderBy, $limit, $offset, $fields);
    }

}
