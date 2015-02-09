<?php

namespace Truelab\KottiModelBundle\Model;
use Truelab\KottiModelBundle\Repository\RepositoryInterface;

/**
 * Interface NodeInterface
 * @package Truelab\KottiModelBundle\Model
 */
interface NodeInterface
{
    /**
     * @return boolean
     */
    public function hasChildren();

    /**
     * @return NodeInterface[]
     */
    public function getChildren();

    /**
     * @return boolean
     */
    public function hasParent();

    /**
     * @return NodeInterface
     */
    public function getParent();

    /**
     * @return string
     */
    public function getPath();


    /**
     * @param RepositoryInterface $repositoryInterface
     *
     * @return mixed
     */
    public function setRepository(RepositoryInterface &$repositoryInterface);
}
