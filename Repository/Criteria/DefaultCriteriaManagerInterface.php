<?php

namespace Truelab\KottiModelBundle\Repository\Criteria;

/**
 * Interface DefaultCriteriaManagerInterface
 * @package Truelab\KottiModelBundle\Repository\Criteria
 */
interface DefaultCriteriaManagerInterface
{
    public function add(DefaultCriteriaInterface $defaultCriteria);

    /**
     * @return array criterias
     */
    public function process();
}
