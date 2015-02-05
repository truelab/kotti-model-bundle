<?php

namespace Truelab\KottiModelBundle\Model;

/**
 * Interface NodeInterface
 * @package Truelab\KottiModelBundle\Model
 */
interface NodeInterface
{
    public function getChildren();

    public function getParent();

    public function getPath();
}
