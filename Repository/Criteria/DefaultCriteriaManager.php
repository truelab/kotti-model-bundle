<?php

namespace Truelab\KottiModelBundle\Repository\Criteria;
use Truelab\KottiModelBundle\Repository\Criteria\Exception\NotUniqueDefaultCriteriaNameException;
use Truelab\KottiModelBundle\Repository\Criteria\Exception\InvalidDefaultCriteriaNameException;

/**
 * Class DefaultCriteriaManager
 * @package Truelab\KottiModelBundle\Repository\Criteria
 */
class DefaultCriteriaManager implements DefaultCriteriaManagerInterface
{
    /**
     * @var DefaultCriteriaInterface[]
     */
    private $defaultCriterias = [];

    /**
     * @param DefaultCriteriaInterface $defaultCriteria
     *
     * @throws InvalidDefaultCriteriaNameException
     * @throws NotUniqueDefaultCriteriaNameException
     */
    public function add(DefaultCriteriaInterface $defaultCriteria)
    {
        $name = $defaultCriteria->getName();

        if(!is_string($name)|| empty($name)) {
            throw new InvalidDefaultCriteriaNameException(get_class($defaultCriteria));
        }

        if(isset($this->defaultCriterias[$name])) {
            throw new NotUniqueDefaultCriteriaNameException($name, get_class($this->defaultCriterias[$name]));
        }

        $this->defaultCriterias[$name] = $defaultCriteria;
    }

    public function process()
    {
        $criterias = [];

        foreach($this->defaultCriterias as $defaultCriteria)
        {
            $criterias[] = $defaultCriteria->getCriteria();
        }

        return $criterias;
    }
}
