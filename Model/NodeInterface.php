<?php

namespace Truelab\KottiModelBundle\Model;
use Truelab\KottiModelBundle\Repository\RepositoryInterface;

/**
 * Interface NodeInterface
 * @package Truelab\KottiModelBundle\Model
 */
interface NodeInterface
{
    public function getChildren();

    public function getParent();

    public function getPath();

    public function setRepository(RepositoryInterface &$repositoryInterface);
}
