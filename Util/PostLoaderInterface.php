<?php

namespace Truelab\KottiModelBundle\Util;

/**
 * Interface PostLoaderInterface
 * @package Truelab\KottiModelBundle\Util
 */
interface PostLoaderInterface
{
    public function support($content);

    public function onPostLoad($content);
}
