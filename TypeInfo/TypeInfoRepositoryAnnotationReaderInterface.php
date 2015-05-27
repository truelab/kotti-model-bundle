<?php

namespace Truelab\KottiModelBundle\TypeInfo;

/**
 * Interface TypeInfoRepositoryAnnotationReaderInterface
 * @package Truelab\KottiModelBundle\TypeInfo
 */
interface TypeInfoRepositoryAnnotationReaderInterface
{
    /**
     * @param string|array $classes
     *
     * @return TypeInfo[]
     */
    public function inheritanceLineageTypeInfos($classes);

    /**
     * @param $class
     *
     * @return null|TypeInfo
     */
    public function typeInfo($class);

    /**
     * @param string|array $aliases
     *
     * @return string|string[]
     */
    public function getClassByAlias($aliases);
}
