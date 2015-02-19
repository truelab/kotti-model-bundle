<?php

namespace Truelab\KottiModelBundle\Model;

/**
 * Interface NodeInterface
 * @package Truelab\KottiModelBundle\Model
 */
interface NodeInterface extends BaseInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @return boolean
     */
    public function hasChildren();

    /**
     * @return self[]
     */
    public function getChildren();

    /**
     * @return boolean
     */
    public function hasParent();

    /**
     * @return self
     */
    public function getParent();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return NodeInterface[]
     */
    public function getSiblings();

    /**
     * @return boolean
     */
    public function isLeaf();

    /**
     * @param self $node
     *
     * @return boolean
     */
    public function equals(self $node);

}
