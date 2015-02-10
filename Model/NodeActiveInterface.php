<?php

namespace Truelab\KottiModelBundle\Model;
use Truelab\KottiModelBundle\Repository\RepositoryInterface;

/**
 * Interface NodeActiveInterface
 * @package Truelab\KottiModelBundle\Model
 */
interface NodeActiveInterface extends NodeInterface
{
    /**
     * @param RepositoryInterface $repositoryInterface
     *
     * @return mixed
     */
    public function setRepository(RepositoryInterface $repositoryInterface);
}
